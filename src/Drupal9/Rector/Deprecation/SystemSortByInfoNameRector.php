<?php

declare(strict_types=1);

namespace DrupalRector\Drupal9\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Rector\VersionBonding\Contract\ComposerPackageConstraintInterface;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class SystemSortByInfoNameRector extends AbstractRector implements ComposerPackageConstraintInterface, DocumentedRuleInterface
{
    public function provideComposerPackageConstraint(): ComposerPackageConstraint
    {
        return new ComposerPackageConstraint('drupal/core', '>=9.3.0');
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\FuncCall);

        if ($this->getName($node->name) !== 'uasort' || !$node->getArgs()[1]->value instanceof Node\Scalar\String_ || $node->getArgs()[1]->value->value !== 'system_sort_by_info_name') {
            return null;
        }

        $args = $this->nodeFactory->createArgs([
            $node->getArgs()[0]->value,
            $this->nodeFactory->createArray([
                $this->nodeFactory->createClassConstFetch('Drupal\Core\Extension\ExtensionList', 'class'),
                'sortByName',
            ]),
        ]);

        $node->args = $args;

        return $node;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Fixes deprecated system_sort_modules_by_info_name() calls',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
uasort($modules, 'system_sort_modules_by_info_name');
CODE_BEFORE,
                    <<<'CODE_AFTER'
uasort($modules, [ModuleExtensionList::class, 'sortByName']);
CODE_AFTER
                ),
            ]
        );
    }
}
