<?php

declare(strict_types=1);

namespace DrupalRector\Drupal9\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNoFieldByIdRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertNoFieldById() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
    $this->assertNoFieldById('name');
    $this->assertNoFieldById('name', 'not the value');
    $this->assertNoFieldById('notexisting', NULL);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
    $this->assertSession()->assertNoFieldById('name');
    $this->assertSession()->fieldValueNotEquals('name', 'not the value');
    $this->assertSession()->fieldNotExists('notexisting');
CODE_AFTER
            ),
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
        if ($this->getName($node->name) !== 'assertNoFieldById') {
            return null;
        }

        $args = $node->args;
        if (count($args) === 3) {
            array_pop($args);
        }
        $assertSessionNode = $this->nodeFactory->createLocalMethodCall('assertSession');

        if (count($args) === 1) {
            return $this->nodeFactory->createMethodCall($assertSessionNode, 'fieldNotExists', [$args[0]]);
        }
        // Check if argument two is a `null` and convert to fieldExists.
        $arg2 = $args[1]->value;
        if ($arg2 instanceof Node\Expr\ConstFetch && strtolower((string) $arg2->name) === 'null') {
            return $this->nodeFactory->createMethodCall($assertSessionNode, 'fieldNotExists', [$args[0]]);
        }

        return $this->nodeFactory->createMethodCall($assertSessionNode, 'fieldValueNotEquals', $args);
    }
}
