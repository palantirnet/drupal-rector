<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ConstructFieldXpathRector extends AbstractRector {


    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated \Drupal\FunctionalTests\AssertLegacyTrait::constructFieldXpath() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->constructFieldXpath('id', 'edit-preferred-admin-langcode')
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->getSession()->getPage()->findField('edit-preferred-admin-langcode')
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
        if ($this->getName($node->name) !== 'constructFieldXpath') {
            return null;
        }
        $args = $node->args;
        $getSessionNode = $this->nodeFactory->createLocalMethodCall('getSession');
        $getPageNode = $this->nodeFactory->createMethodCall($getSessionNode, 'getPage');
        return $this->nodeFactory->createMethodCall($getPageNode, 'findField', [$args[1]]);
    }

}
