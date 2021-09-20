<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertFieldByNameRector extends AbstractRector
{

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertFieldByName() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertFieldByName('field_name', 'expected_value');
$this->assertFieldByName("field_name[0][value][date]", '', 'Date element found.');
$this->assertFieldByName("field_name[0][value][time]", null, 'Time element found.');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->fieldValueEquals('field_name', 'expected_value');
$this->assertSession()->fieldValueEquals("field_name[0][value][date]", '');
$this->assertSession()->fieldExists("field_name[0][value][time]");
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
        if ($this->getName($node->name) !== 'assertFieldByName') {
            return null;
        }

        $args = $node->args;
        if (count($args) === 3) {
            array_pop($args);
        }
        $assertSessionNode = $this->nodeFactory->createLocalMethodCall('assertSession');

        // If we have one argument, change to fieldExists and return early.
        if (count($args) === 1) {
            return $this->nodeFactory->createMethodCall($assertSessionNode, 'fieldExists', $args);
        }
        // Check if argument two is a `null` and convert to fieldExists.
        $arg2 = $args[1]->value;
        if ($arg2 instanceof Node\Expr\ConstFetch && strtolower((string) $arg2->name) === 'null') {
            return $this->nodeFactory->createMethodCall($assertSessionNode, 'fieldExists', [$args[0]]);
        }

        return $this->nodeFactory->createMethodCall($assertSessionNode, 'fieldValueEquals', $args);
    }
}
