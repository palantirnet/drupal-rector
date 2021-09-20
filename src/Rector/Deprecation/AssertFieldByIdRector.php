<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertFieldByIdRector extends AbstractRector
{

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertFieldById() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
    $this->assertFieldById('edit-name', NULL);
    $this->assertFieldById('edit-name', 'Test name');
    $this->assertFieldById('edit-description', NULL);
    $this->assertFieldById('edit-description');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
    $this->assertSession()->fieldExists('edit-name');
    $this->assertSession()->fieldValueEquals('edit-name', 'Test name');
    $this->assertSession()->fieldExists('edit-description');
    $this->assertSession()->fieldValueEquals('edit-description', '');
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
        if ($this->getName($node->name) !== 'assertFieldById') {
            return null;
        }

        $args = $node->args;
        if (count($args) === 3) {
            array_pop($args);
        }
        $assertSessionNode = $this->nodeFactory->createLocalMethodCall('assertSession');

        if (count($args) === 1) {
            $args[] = $this->nodeFactory->createArg('');
            return $this->nodeFactory->createMethodCall($assertSessionNode, 'fieldValueEquals', $args);
        }
        // Check if argument two is a `null` and convert to fieldExists.
        $arg2 = $args[1]->value;
        if ($arg2 instanceof Node\Expr\ConstFetch && strtolower((string) $arg2->name) === 'null') {
            return $this->nodeFactory->createMethodCall($assertSessionNode, 'fieldExists', [$args[0]]);
        }

        return $this->nodeFactory->createMethodCall($assertSessionNode, 'fieldValueEquals', $args);
    }
}
