<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Drupal10\Rector\ValueObject\AnnotationToAttributeConfiguration;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use Rector\BetterPhpDocParser\PhpDoc\ArrayItemNode;
use Rector\BetterPhpDocParser\PhpDoc\DoctrineAnnotationTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocInfo\TokenIteratorFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\BetterPhpDocParser\PhpDocParser\StaticDoctrineAnnotationParser\ArrayParser;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\PhpAttribute\AnnotationToAttributeMapper;
use Rector\ValueObject\PhpVersion;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://docs.phpunit.de/en/10.0/annotations.html#ticket
 *
 * @see \Rector\PHPUnit\Tests\AnnotationsToAttributes\Rector\Class_\TicketAnnotationToAttributeRector\TicketAnnotationToAttributeRectorTest
 */
final class AnnotationToAttributeRector extends AbstractDrupalCoreRector implements MinPhpVersionInterface
{
    /**
     * @var array|AnnotationToAttributeConfiguration[]
     */
    protected array $configuration = [];

    private PhpDocTagRemover $phpDocTagRemover;

    private DocBlockUpdater $docBlockUpdater;

    private ArrayParser $arrayParser;

    private TokenIteratorFactory $tokenIteratorFactory;

    private PhpDocInfoFactory $phpDocInfoFactory;

    /**
     * @var AnnotationToAttributeMapper
     */
    private AnnotationToAttributeMapper $annotationToAttributeMapper;

    public function __construct(PhpDocTagRemover $phpDocTagRemover, DocBlockUpdater $docBlockUpdater, PhpDocInfoFactory $phpDocInfoFactory, ArrayParser $arrayParser, TokenIteratorFactory $tokenIteratorFactory, AnnotationToAttributeMapper $annotationToAttributeMapper)
    {
        $this->phpDocTagRemover = $phpDocTagRemover;
        $this->docBlockUpdater = $docBlockUpdater;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
        $this->arrayParser = $arrayParser;
        $this->tokenIteratorFactory = $tokenIteratorFactory;
        $this->annotationToAttributeMapper = $annotationToAttributeMapper;
    }

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof AnnotationToAttributeConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalIntroducedVersionConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change annotations with value to attribute', [new ConfiguredCodeSample(<<<'CODE_SAMPLE'

namespace Drupal\Core\Action\Plugin\Action;

use Drupal\Core\Session\AccountInterface;

/**
 * Publishes an entity.
 *
 * @Action(
 *   id = "entity:publish_action",
 *   action_label = @Translation("Publish"),
 *   deriver = "Drupal\Core\Action\Plugin\Action\Derivative\EntityPublishedActionDeriver",
 * )
 */
class PublishAction extends EntityActionBase {
CODE_SAMPLE
            , <<<'CODE_SAMPLE'

namespace Drupal\Core\Action\Plugin\Action;

use Drupal\Core\Action\Plugin\Action\Derivative\EntityPublishedActionDeriver;
use Drupal\Core\Action\Attribute\Action;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Publishes an entity.
 */
#[Action(
  id: 'entity:publish_action',
  action_label: new TranslatableMarkup('Publish'),
  deriver: EntityPublishedActionDeriver::class
)]
class PublishAction extends EntityActionBase {
CODE_SAMPLE
            ,
            [
                new AnnotationToAttributeConfiguration('10.2.0', '12.0.0', 'Action', 'Drupal\Core\Action\Attribute\Action'),
            ])]);
    }

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

    /**
     * @param Class_|ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        foreach ($this->configuration as $configuration) {
            if ($this->rectorShouldApplyToDrupalVersion($configuration) === false) {
                continue;
            }

            $result = $this->refactorWithConfiguration($node, $configuration);

            // Skip if no result.
            if ($result === null) {
                continue;
            }

            return $result;
        }

        return null;
    }

    /**
     * @param Class_|ClassMethod                 $node
     * @param AnnotationToAttributeConfiguration $configuration
     */
    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($node);
        if (!$phpDocInfo instanceof PhpDocInfo) {
            return null;
        }

        $tagsByName = $phpDocInfo->getTagsByName($configuration->getAnnotation());
        if ($tagsByName === []) {
            return null;
        }

        $hasAttribute = false;
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($attr->name->toString() === $configuration->getAttributeClass()) {
                    $hasAttribute = true;
                    break 2;
                }
            }
        }

        $docBlockHasChanged = \false;
        foreach ($tagsByName as $valueNode) {
            if (!$valueNode->value instanceof GenericTagValueNode) {
                continue;
            }

            if ($hasAttribute === false) {
                $stringValue = $valueNode->value->value;
                $stringValue = '{'.trim($stringValue, '()').'}';
                $tokenIterator = $this->tokenIteratorFactory->create($stringValue);
                $data = $this->arrayParser->parseCurlyArray($tokenIterator, $node);
                $attribute = $this->createAttribute($configuration->getAttributeClass(), $data);
                $node->attrGroups[] = new AttributeGroup([$attribute]);
            }

            if (version_compare($this->installedDrupalVersion(), $configuration->getRemoveVersion(), '>=')) {
                $this->phpDocTagRemover->removeTagValueFromNode($phpDocInfo, $valueNode);
                $docBlockHasChanged = \true;
            }
        }
        if ($docBlockHasChanged) {
            $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);

            return $node;
        }

        return null;
    }

    /**
     * @param array|ArrayItemNode[] $parsedArgs
     *
     * @return Attribute
     */
    private function createAttribute(string $attributeClass, array $parsedArgs): Attribute
    {
        $fullyQualified = new FullyQualified($attributeClass);
        $args = [];
        foreach ($parsedArgs as $value) {
            if ($value->key == 'deriver') {
                $arg = $this->nodeFactory->createClassConstFetch($value->value->value, 'class');
            } elseif ($value->value instanceof DoctrineAnnotationTagValueNode) {
                $arg = $this->convertAnnotation($value->value);
            } else {
                $attribute = $this->annotationToAttributeMapper->map($value);
                $arg = $attribute->value;
            }

            // Sometimes the end ) matches. We need to remove it.
            if ($value->key === null) {
                continue;
            }

            $args[] = new Arg($arg, \false, \false, [], new Node\Identifier($value->key));
        }

        return new Attribute($fullyQualified, $args);
    }

    public function convertAnnotation(DoctrineAnnotationTagValueNode $value): ?Node\Expr
    {
        return match ($value->identifierTypeNode->name) {
            '@Translation' => $this->convertTranslateAnnotation($value),
            '@PluralTranslation' => $this->convertPluralTranslationAnnotation($value),
            default => null,
        };
    }

    public function convertPluralTranslationAnnotation(DoctrineAnnotationTagValueNode $value): ?Node\Expr
    {
        // Check the annotation type, this will be helpful later.
        if ($value->identifierTypeNode->name !== '@PluralTranslation') {
            return null;
        }

        return $this->nodeFactory->createArray([
            $value->values[0]->key => $value->values[0]->value->value,
            $value->values[1]->key => $value->values[1]->value->value,
        ]);
    }

    public function convertTranslateAnnotation(DoctrineAnnotationTagValueNode $value): ?Node\Expr\New_
    {
        // Check the annotation type, this will be helpful later.
        if ($value->identifierTypeNode->name !== '@Translation') {
            return null;
        }

        $valueArg = null;
        $argumentArg = null;
        $contextArg = null;

        // Loop through the values of the annotation, just to make 100% sure we have the correct argument order
        foreach ($value->values as $translateValue) {
            if ($translateValue->key === null) {
                $valueArg = $this->nodeFactory->createArg($translateValue->value->value);
            }
            if ($translateValue->key === 'context') {
                $contextArg = $this->nodeFactory->createArg(['context' => $translateValue->value->value]);
            }
            if ($translateValue->key === 'arguments') {
                $argumentArg = [];
                foreach ($translateValue->value->values as $argumentValue) {
                    $argumentArg[$argumentValue->key->value] = $argumentValue->value->value;
                }
                $argumentArg = $this->nodeFactory->createArg($argumentArg);
            }
        }

        $argArray = [];
        if ($valueArg !== null) {
            $argArray[] = $valueArg;
        }
        if ($argumentArg !== null) {
            $argArray[] = $argumentArg;
        }
        if ($contextArg !== null) {
            $argArray[] = $contextArg;
        }

        return new Node\Expr\New_(new Node\Name('\Drupal\Core\StringTranslation\TranslatableMarkup'), $argArray);
    }
}
