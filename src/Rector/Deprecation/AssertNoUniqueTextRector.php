<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\GetDeclaringSourceTrait;
use PhpParser\Node;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\Rector\AbstractRector;
use Rector\PostRector\Collector\NodesToAddCollector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNoUniqueTextRector extends AbstractRector
{

    /**
     * @readonly
     * @var \Rector\PostRector\Collector\NodesToAddCollector
     */
    private $nodesToAddCollector;

    public function __construct(NodesToAddCollector $nodesToAddCollector)
    {
        $this->nodesToAddCollector = $nodesToAddCollector;
    }

    use GetDeclaringSourceTrait;

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertNoUniqueText() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertNoUniqueText('Duplicated message');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$page_text = $this->getSession()->getPage()->getText();
$nr_found = substr_count($page_text, 'Duplicated message');
$this->assertGreaterThan(1, $nr_found, "'Duplicated message' found more than once on the page");
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
        if ($this->getDeclaringSource($node) !== 'Drupal\FunctionalTests\AssertLegacyTrait') {
            return null;
        }
        if (count($node->args) === 0) {
            throw new ShouldNotHappenException('assertNoUniqueText had no arguments');
        }

        $nodes = [];

        $getSessionNode = $this->nodeFactory->createLocalMethodCall('getSession');
        $getPageNode = $this->nodeFactory->createMethodCall($getSessionNode, 'getPage');
        $getTextNode = $this->nodeFactory->createMethodCall($getPageNode, 'getText');
        $pageTextVar = new Node\Expr\Variable('page_text');
        // @phpstan-ignore-next-line
        $this->nodesToAddCollector->addNodeBeforeNode(new Node\Expr\Assign($pageTextVar, $getTextNode), $node);

        $nrFoundVar = new Node\Expr\Variable('nr_found');
        $substrCountNode = $this->nodeFactory->createFuncCall(
            'substr_count',
            [new Node\Arg($pageTextVar), $node->args[0]]
        );
        // @phpstan-ignore-next-line
        $this->nodesToAddCollector->addNodeBeforeNode(new Node\Expr\Assign($nrFoundVar, $substrCountNode), $node);

        $assertedText = $node->args[0]->value;
        if ($assertedText instanceof Node\Scalar\String_) {
            $assertedText = new Node\Scalar\EncapsedStringPart($assertedText->value);
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
