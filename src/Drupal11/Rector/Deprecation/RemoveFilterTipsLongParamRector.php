<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes the deprecated $long parameter from FilterInterface::tips() and _filter_tips().
 *
 * The long-format filter-tips page was deprecated in drupal:11.4.0 and will
 * be removed in drupal:12.0.0. This rector removes the $long parameter from
 * tips() method signatures in classes extending FilterBase or implementing
 * FilterInterface (only when $long is not referenced in the body), and drops
 * the second argument from _filter_tips() calls.
 *
 * When $long is used inside the method body the rule skips that method —
 * the developer must manually remove the long-tip branch.
 *
 * @see https://www.drupal.org/node/3505370
 * @see https://www.drupal.org/node/3567879
 */
class RemoveFilterTipsLongParamRector extends AbstractRector
{
    private const FILTER_SYMBOLS = [
        'Drupal\\filter\\Plugin\\FilterBase',
        'Drupal\\filter\\Plugin\\FilterInterface',
        'FilterBase',
        'FilterInterface',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove deprecated $long parameter from FilterInterface::tips() implementations and from _filter_tips() calls.',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
public function tips($long = FALSE) {
    return $this->t('No HTML tags allowed.');
}
CODE_BEFORE,
                    <<<'CODE_AFTER'
public function tips() {
    return $this->t('No HTML tags allowed.');
}
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Class_::class, FuncCall::class];
    }

    /** @param Class_|FuncCall $node */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof Class_) {
            return $this->refactorClass($node);
        }

        return $this->refactorFuncCall($node);
    }

    private function refactorClass(Class_ $node): ?Class_
    {
        if (!$this->classIsFilterPlugin($node)) {
            return null;
        }

        $changed = false;
        foreach ($node->getMethods() as $method) {
            if ($this->updateTipsMethod($method)) {
                $changed = true;
            }
        }

        return $changed ? $node : null;
    }

    private function classIsFilterPlugin(Class_ $classNode): bool
    {
        foreach ($classNode->implements as $implement) {
            if ($this->nameMatchesFilterSymbol($implement)) {
                return true;
            }
        }

        if ($classNode->extends !== null && $this->nameMatchesFilterSymbol($classNode->extends)) {
            return true;
        }

        return false;
    }

    private function nameMatchesFilterSymbol(Name $name): bool
    {
        return in_array($name->toString(), self::FILTER_SYMBOLS, true);
    }

    private function updateTipsMethod(ClassMethod $method): bool
    {
        if (!$this->isName($method, 'tips')) {
            return false;
        }

        if (count($method->params) === 0) {
            return false;
        }

        $firstParam = $method->params[0];
        if (!$firstParam->var instanceof Variable) {
            return false;
        }

        if (!$this->isName($firstParam->var, 'long')) {
            return false;
        }

        $usesLong = false;
        $this->traverseNodesWithCallable((array) $method->stmts, function (Node $subNode) use (&$usesLong): ?Node {
            if ($subNode instanceof Variable && $this->isName($subNode, 'long')) {
                $usesLong = true;
            }

            return null;
        });

        if ($usesLong) {
            return false;
        }

        $method->params = [];

        return true;
    }

    private function refactorFuncCall(FuncCall $node): ?FuncCall
    {
        if (!$this->isName($node->name, '_filter_tips')) {
            return null;
        }

        if (count($node->getArgs()) < 2) {
            return null;
        }

        array_splice($node->args, 1);

        return $node;
    }
}
