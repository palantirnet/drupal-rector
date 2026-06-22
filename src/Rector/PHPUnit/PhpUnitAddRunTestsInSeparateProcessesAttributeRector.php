<?php

declare(strict_types=1);

namespace DrupalRector\Rector\PHPUnit;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use Rector\PHPStan\ScopeFetcher;
use Rector\ValueObject\PhpVersion;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Additively stamps #[RunTestsInSeparateProcesses] on KernelTestBase and
 * BrowserTestBase subclasses for PHPUnit 12 / Drupal 12 readiness.
 *
 * Skips: anonymous classes, abstract classes, UnitTestCase subclasses (not Kernel/Browser),
 * and classes already carrying the attribute (idempotent).
 *
 * @see https://www.drupal.org/project/drupal/issues/3445240
 * @see https://git.drupalcode.org/project/rector/-/work_items/3552124
 */
final class PhpUnitAddRunTestsInSeparateProcessesAttributeRector extends AbstractDrupalCoreRector implements MinPhpVersionInterface
{
    private const ATTRIBUTE = 'PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses';

    /**
     * This additive rector emits NO PHPStan deprecation notice — adding
     * #[RunTestsInSeparateProcesses] is a test-execution change, not a
     * conversion of a @deprecated PHP symbol.
     *
     * @var array<string>
     */
    public const PHPSTAN_MESSAGES = [];

    /**
     * @var string[]
     */
    private const TARGET_BASE_CLASSES = [
        'Drupal\KernelTests\KernelTestBase',
        'Drupal\Tests\BrowserTestBase',
    ];

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function provideMinPhpVersion(): int
    {
        return PhpVersion::PHP_81;
    }

    // refactor() is overridden to drive the additive change directly; the parent's Expr BC-wrap path does not apply to a Class_ statement. The abstract refactorWithConfiguration() stub below is required by the base but unused.
    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_ || $node->isAnonymous() || $node->isAbstract()) {
            return null;
        }

        $versionGatePasses = false;
        foreach ($this->configuration as $configuration) {
            if ($this->rectorShouldApplyToDrupalVersion($configuration)) {
                $versionGatePasses = true;
                break;
            }
        }
        if ($versionGatePasses === false) {
            return null;
        }

        if (!$this->isTargetTestClass($node)) {
            return null;
        }

        if ($this->hasAttribute($node)) {
            return null;
        }

        $node->attrGroups[] = new AttributeGroup([new Attribute(new FullyQualified(self::ATTRIBUTE))]);

        return $node;
    }

    private function isTargetTestClass(Class_ $node): bool
    {
        $scope = class_exists(ScopeFetcher::class) ? ScopeFetcher::fetch($node) : $node->getAttribute('scope');
        if ($scope === null) {
            return false;
        }
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return false;
        }
        foreach (self::TARGET_BASE_CLASSES as $baseClass) {
            if ($classReflection->isSubclassOf($baseClass)) {
                return true;
            }
        }

        return false;
    }

    private function hasAttribute(Class_ $node): bool
    {
        // Compare on the short (last) name segment rather than the fully-qualified
        // string. After Rector's name-importing pass the attribute is reprinted as
        // a short, `use`-imported name (`#[RunTestsInSeparateProcesses]`); on a
        // subsequent pass its `Name` node resolves to the short form (or, without a
        // matching import, to the current namespace), so a fully-qualified
        // comparison never matches and the rule re-stamps a duplicate on every pass
        // — an unbounded attribute stack. The short-name check is
        // import-resolution-agnostic and keeps the rule idempotent.
        $parts = explode('\\', self::ATTRIBUTE);
        $target = end($parts);
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($attr->name->getLast() === $target) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add #[RunTestsInSeparateProcesses] to Kernel and Browser test classes (Drupal 12 / PHPUnit 12 readiness).', [
            new CodeSample(
                <<<'CODE_BEFORE'
                    use Drupal\KernelTests\KernelTestBase;

                    final class SomeKernelTest extends KernelTestBase {}
                    CODE_BEFORE,
                <<<'CODE_AFTER'
                    use Drupal\KernelTests\KernelTestBase;

                    #[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
                    final class SomeKernelTest extends KernelTestBase {}
                    CODE_AFTER
            ),
        ]);
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration)
    {
        return null;
    }
}
