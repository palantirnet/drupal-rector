<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class PassRector extends AbstractRector
{

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::pass() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->pass('The whole transaction is rolled back when a duplicate key insert occurs.');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
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
        if ($this->getName($node->name) !== 'pass') {
            return null;
        }

        $this->removeNode($node);
        return $node;
    }
}
