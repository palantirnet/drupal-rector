<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use PhpParser\Node;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\Rector\AbstractRector;
use Rector\PostRector\Collector\NodesToAddCollector;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNoUniqueTextRector extends AbstractRector
{

    /**
     * @todo remove when property is no longer private in AbstractRector.
     */
    private $nodesToAddCollector;

    public function __construct(
        NodesToAddCollector $nodesToAddCollector
    ) {
        $this->nodesToAddCollector = $nodesToAddCollector;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated \Drupal\FunctionalTests\AssertLegacyTrait::assertUniqueText() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertUniqueText('Color set');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->getSession()->pageTextContainsOnce('Color set')
CODE_AFTER
            )
        ]);
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Expr\MethodCall::class,
        ];
    }

    public function refactor(Node $node)
    {
        assert($node instanceof Node\Expr\MethodCall);
        if ($this->getName($node->name) !== 'assertNoUniqueText') {
            return null;
        }
        if (count($node->args) === 0) {
            throw new ShouldNotHappenException('assertNoUniqueText had no arguments');
        }

        $getSessionNode = $this->nodeFactory->createLocalMethodCall('getSession');
        $getPageNode = $this->nodeFactory->createMethodCall($getSessionNode, 'getPage');
        $getTextNode = $this->nodeFactory->createMethodCall($getPageNode, 'getText');
        $pageTextVar = new Node\Expr\Variable('page_text');
        $this->nodesToAddCollector->addNodeBeforeNode(new Node\Expr\Assign($pageTextVar, $getTextNode), $node);

        $nrFoundVar = new Node\Expr\Variable('nr_found');
        $substrCountNode = $this->nodeFactory->createFuncCall(
            'substr_count',
            [new Node\Arg($pageTextVar), $node->args[0]]
        );
        $this->nodesToAddCollector->addNodeBeforeNode(new Node\Expr\Assign($nrFoundVar, $substrCountNode), $node);

        $assertedText = $node->args[0]->value;
        if ($assertedText instanceof Node\Scalar\String_) {
            $assertedText = $assertedText->value;
        } elseif (!$assertedText instanceof Node\Expr\Variable) {
            throw new \RuntimeException(__CLASS__ . ' cannot handle argument of type ' . get_class($assertedText));
        }

        return $this->nodeFactory->createLocalMethodCall(
            'assertGreaterThan',
            [
                new Node\Arg(new Node\Scalar\LNumber(1)),
                new Node\Arg($nrFoundVar),
                // "'$assertedText' found more than once on the page"
                new Node\Arg(new Node\Scalar\Encapsed([
                    new Node\Scalar\EncapsedStringPart("'"),
                    $assertedText,
                    new Node\Scalar\EncapsedStringPart("' found more than once on the page"),
                ]))
            ]
        );
    }
}
