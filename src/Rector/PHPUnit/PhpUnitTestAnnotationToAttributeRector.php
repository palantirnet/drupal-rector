<?php

declare(strict_types=1);

namespace DrupalRector\Rector\PHPUnit;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\PHPUnit\ValueObject\PhpUnitTestAnnotationToAttributeConfiguration;
use DrupalRector\Services\DrupalRectorSettings;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\PHPStan\ScopeFetcher;
use Rector\ValueObject\PhpVersion;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Converts PHPUnit test metadata annotations to PHP attributes.
 *
 * Backward-compatible: the annotation is kept alongside the new attribute while
 * the configured removeVersion has not yet been reached (BC on) or not yet
 * installed.  Only a Drupal >= removeVersion install or an opted-in clean rewrite
 * strips the annotation (BC off).
 *
 * @see https://www.drupal.org/project/drupal/issues/3535662
 * @see https://www.drupal.org/project/drupal/issues/3417066
 * @see https://git.drupalcode.org/project/rector/-/work_items/3552124
 */
final class PhpUnitTestAnnotationToAttributeRector extends AbstractDrupalCoreRector implements MinPhpVersionInterface
{
    /**
     * These conversions emit NO PHPStan deprecation notice — the trigger is a
     * docblock-metadata convention (not a @deprecated PHP symbol), so there is
     * no matching error to suppress.
     *
     * @var array<string>
     */
    public const PHPSTAN_MESSAGES = [];

    public function __construct(
        private readonly DrupalRectorSettings $drupalRectorSettings,
        private readonly PhpDocTagRemover $phpDocTagRemover,
        private readonly DocBlockUpdater $docBlockUpdater,
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
    ) {
        parent::__construct($drupalRectorSettings);
    }

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof PhpUnitTestAnnotationToAttributeConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', PhpUnitTestAnnotationToAttributeConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class, ClassMethod::class];
    }

    public function provideMinPhpVersion(): int
    {
        return PhpVersion::PHP_81;
    }

    public function refactor(Node $node): ?Node
    {
        if (!$this->isInsideTestCase($node)) {
            return null;
        }

        if (!$node instanceof Class_ && !$node instanceof ClassMethod) {
            return null;
        }

        $changed = false;
        foreach ($this->configuration as $configuration) {
            if ($this->rectorShouldApplyToDrupalVersion($configuration) === false) {
                continue;
            }
            if (!$configuration instanceof PhpUnitTestAnnotationToAttributeConfiguration) {
                continue;
            }
            if ($this->refactorTag($node, $configuration)) {
                $changed = true;
            }
        }

        return $changed ? $node : null;
    }

    private function refactorTag(Class_|ClassMethod $node, PhpUnitTestAnnotationToAttributeConfiguration $configuration): bool
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($node);
        if (!$phpDocInfo instanceof PhpDocInfo) {
            return false;
        }

        $tags = $phpDocInfo->getTagsByName($configuration->getAnnotation());
        if ($tags === []) {
            return false;
        }

        $removeAnnotation =
            !$this->drupalRectorSettings->isBackwardCompatibilityEnabled()
            || version_compare($this->installedDrupalVersion(), $configuration->getRemoveVersion(), '>=');

        $changed = false;
        foreach ($tags as $tagNode) {
            if (!$tagNode->value instanceof GenericTagValueNode) {
                continue;
            }

            $attributes = $this->buildAttributes($configuration->getAnnotation(), $tagNode->value->value, $configuration->getAttributeClass());
            if ($attributes === []) {
                // Unsupported value form: leave the annotation untouched.
                continue;
            }

            foreach ($attributes as $attribute) {
                if ($this->attributeAlreadyPresent($node, $attribute)) {
                    continue;
                }
                $node->attrGroups[] = new AttributeGroup([$attribute]);
                $changed = true;
            }

            if ($removeAnnotation) {
                $this->phpDocTagRemover->removeTagValueFromNode($phpDocInfo, $tagNode);
                $changed = true;
            }
        }

        if ($changed) {
            $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);
        }

        return $changed;
    }

    /**
     * @return Attribute[]
     */
    private function buildAttributes(string $annotation, string $rawValue, string $attributeClass): array
    {
        return match ($annotation) {
            'group' => $this->convertGroup($rawValue, $attributeClass),
            default => [],
        };
    }

    /**
     * @return Attribute[]
     */
    private function convertGroup(string $rawValue, string $attributeClass): array
    {
        $value = trim($rawValue);
        if ($value === '') {
            return [];
        }
        if ($value === 'legacy') {
            return [new Attribute(new FullyQualified('PHPUnit\Framework\Attributes\IgnoreDeprecations'))];
        }

        return [new Attribute(new FullyQualified($attributeClass), [new Arg(new String_($value))])];
    }

    private function attributeAlreadyPresent(Class_|ClassMethod $node, Attribute $candidate): bool
    {
        $candidateClass = ltrim($candidate->name->toString(), '\\');
        $candidateValue = $this->firstStringArgValue($candidate);

        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if (ltrim($attr->name->toString(), '\\') !== $candidateClass) {
                    continue;
                }
                if ($candidateValue === null) {
                    return true;
                }
                if ($this->firstStringArgValue($attr) === $candidateValue) {
                    return true;
                }
            }
        }

        return false;
    }

    private function firstStringArgValue(Attribute $attribute): ?string
    {
        if ($attribute->args === []) {
            return null;
        }
        $first = $attribute->args[0];
        if ($first->value instanceof String_) {
            return $first->value->value;
        }

        return null;
    }

    private function isInsideTestCase(Node $node): bool
    {
        $scope = class_exists(ScopeFetcher::class) ? ScopeFetcher::fetch($node) : $node->getAttribute('scope');
        if ($scope === null) {
            return false;
        }
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return false;
        }

        return $classReflection->isSubclassOf(\PHPUnit\Framework\TestCase::class);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Convert PHPUnit test metadata annotations to attributes (backward-compatible: keeps the annotation while a supported older version remains).', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
                    /**
                     * @group example
                     */
                    public function testThing(): void {}
                    CODE_BEFORE,
                <<<'CODE_AFTER'
                    /**
                     * @group example
                     */
                    #[\PHPUnit\Framework\Attributes\Group('example')]
                    public function testThing(): void {}
                    CODE_AFTER,
                [
                    new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'group', 'PHPUnit\Framework\Attributes\Group'),
                ]
            ),
        ]);
    }

    /**
     * Not used: refactor() is overridden and drives conversion directly.
     */
    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration)
    {
        return null;
    }
}
