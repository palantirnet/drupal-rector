<?php declare (strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use Drupal\Core\Action\Attribute\Action;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use Rector\BetterPhpDocParser\PhpDoc\ArrayItemNode;
use Rector\BetterPhpDocParser\PhpDocInfo\TokenIteratorFactory;
use Rector\Core\ValueObject\PhpVersion;
use RectorPrefix202310\PHPUnit\Framework\Attributes\Ticket;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\BetterPhpDocParser\PhpDocParser\StaticDoctrineAnnotationParser\ArrayParser;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\Core\Rector\AbstractRector;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @changelog https://docs.phpunit.de/en/10.0/annotations.html#ticket
 *
 * @see \Rector\PHPUnit\Tests\AnnotationsToAttributes\Rector\Class_\TicketAnnotationToAttributeRector\TicketAnnotationToAttributeRectorTest
 */
final class ActionAnnotationToAttributeRector extends AbstractRector implements MinPhpVersionInterface
{
    /**
     * @readonly
     * @var \Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover
     */
    private $phpDocTagRemover;
    /**
     * @readonly
     * @var \Rector\Comments\NodeDocBlock\DocBlockUpdater
     */
    private $docBlockUpdater;
    /**
     * @readonly
     * @var \Rector\BetterPhpDocParser\PhpDocParser\StaticDoctrineAnnotationParser\ArrayParser
     */
    private $arrayParser;
    /**
     * @readonly
     * @var \Rector\BetterPhpDocParser\PhpDocInfo\TokenIteratorFactory
     */
    private $tokenIteratorFactory;
    /**
     * @readonly
     * @var \Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory
     */
    private $phpDocInfoFactory;
    public function __construct(PhpDocTagRemover $phpDocTagRemover, DocBlockUpdater $docBlockUpdater, PhpDocInfoFactory $phpDocInfoFactory, ArrayParser $arrayParser, TokenIteratorFactory $tokenIteratorFactory)
    {
        $this->phpDocTagRemover = $phpDocTagRemover;
        $this->docBlockUpdater = $docBlockUpdater;
        $this->phpDocInfoFactory = $phpDocInfoFactory;
        $this->arrayParser = $arrayParser;
        $this->tokenIteratorFactory = $tokenIteratorFactory;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Change annotations with value to attribute', [new CodeSample(<<<'CODE_SAMPLE'

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
)]);
    }
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return [Class_::class];
    }
    public function provideMinPhpVersion() : int
    {
        return PhpVersion::PHP_81;
    }
    /**
     * @param Class_|ClassMethod $node
     */
    public function refactor(Node $node) : ?Node
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFromNode($node);
        if (!$phpDocInfo instanceof PhpDocInfo) {
            return null;
        }
        /** @var PhpDocTagNode[] $tagsByName */
        $tagsByName = $phpDocInfo->getTagsByName('Action');
        if ($tagsByName === []) {
            return null;
        }
        $hasChanged = \false;
        foreach ($tagsByName as $valueNode) {
            if (!$valueNode->value instanceof GenericTagValueNode) {
                continue;
            }
            $stringValue = $valueNode->value->value;
            $stringValue = '{' . trim($stringValue, '()') . '}';
            $tokenIterator = $this->tokenIteratorFactory->create($stringValue);
            $data = $this->arrayParser->parseCurlyArray($tokenIterator, $node);
            $attribute = $this->createAttribute($data);
            $node->attrGroups[] = new AttributeGroup([$attribute]);
            // cleanup
            $this->phpDocTagRemover->removeTagValueFromNode($phpDocInfo, $valueNode);
            $hasChanged = \true;
        }
        if ($hasChanged) {
            $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);
            return $node;
        }
        return null;
    }
    private function createAttribute(array $parsedArgs) : Attribute
    {
        $fullyQualified = new FullyQualified(Action::class);
        $args = [];
        foreach ($parsedArgs as $value) {
            if ($value->key === 'label') {
                $arg = new Node\Expr\New_(new Node\Name(TranslatableMarkup::class), [new Arg(new String_($value->value->values[0]->value->value))]);
            }
            else {
                $arg = new String_($value->value->value);
            }
            $args[] = new Arg($arg, \false, \false, [], new Node\Identifier($value->key));
        }
        return new Attribute($fullyQualified, $args);
    }
}
