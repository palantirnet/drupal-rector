<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertOptionSelectedRector extends AbstractRector
{

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertOptionSelected() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
    $this->assertOptionSelected('options', 2);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
    $this->assertTrue($this->assertSession()->optionExists('options', 2)->hasAttribute('selected'));
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
        assert($node instanceof Node\Expr\MethodCall);
        if ($this->getName($node->name) !== 'assertOptionSelected') {
            return null;
        }

        $message = null;
        if (count($node->args) === 3) {
            $message = $node->args[2];
        }

        $assertSessionNode = $this->nodeFactory->createLocalMethodCall('assertSession');
        $optionExistsNode = $this->nodeFactory->createMethodCall($assertSessionNode, 'optionExists', [
            $node->args[0],
            $node->args[1],
        ]);
        $hasAttributeNode = $this->nodeFactory->createMethodCall(
            $optionExistsNode,
            'hasAttribute',
            $this->nodeFactory->createArgs(['selected'])
        );

        if ($message === null) {
            return $this->nodeFactory->createLocalMethodCall('assertTrue', [
                $this->nodeFactory->createArg($hasAttributeNode),
            ]);
        }

        return $this->nodeFactory->createLocalMethodCall('assertTrue', [
            $this->nodeFactory->createArg($hasAttributeNode),
            $this->nodeFactory->createArg($message)
        ]);
    }

}
