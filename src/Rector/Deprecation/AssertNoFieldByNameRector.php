<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\AddCommentTrait;
use DrupalRector\Utility\GetDeclaringSourceTrait;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNoFieldByNameRector extends AbstractRector
{
    use AddCommentTrait;
    use GetDeclaringSourceTrait;

    protected string $deprecatedMethodName = 'assertNoFieldByName';
    protected string $methodName = 'fieldNotExists';
    protected string $comment = 'Verify the assertion: buttonNotExists() if this is for a button.';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertNoFieldByName() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
    $this->assertNoFieldByName('name');
    $this->assertNoFieldByName('name', 'not the value');
    $this->assertNoFieldByName('notexisting');
    $this->assertNoFieldByName('notexisting', NULL);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
    $this->assertSession()->fieldValueNotEquals('name', '');
    $this->assertSession()->fieldValueNotEquals('name', 'not the value');
    $this->assertSession()->fieldValueNotEquals('notexisting', '');
    $this->assertSession()->fieldNotExists('notexisting');
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

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Stmt\Expression);

        if (!($node->expr instanceof Node\Expr\MethodCall)) {
            return null;
        }

        $expr = $node->expr;

        if ($this->getName($expr->name) !== $this->deprecatedMethodName) {
            return null;
        }

        $args = $expr->args;
        // If there was only one argument, we have to apply the default empty
        // string for the $value parameter.
        if (count($args) === 1) {
            $args[] = $this->nodeFactory->createArg('');
            $node->expr = $this->createAssertSessionMethodCall('fieldValueNotEquals', $args);
            return $node;
        }

        $valueArg = $args[1]->value;
        if ($valueArg instanceof Node\Expr\ConstFetch && \strtolower($valueArg->name->toString()) === 'null') {
            $this->addDrupalRectorComment($node, $this->comment);
            $node->expr = $this->createAssertSessionMethodCall('fieldNotExists', [$args[0]]);
            return $node;
        }

        $node->expr = $this->createAssertSessionMethodCall('fieldValueNotEquals', $args);
        return $node;
    }

    protected function createAssertSessionMethodCall(string $method, array $args): Node\Expr\MethodCall
    {
        $assertSessionNode = $this->nodeFactory->createLocalMethodCall('assertSession');
        return $this->nodeFactory->createMethodCall($assertSessionNode, $method, $args);
    }
}
