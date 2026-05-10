<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces the deprecated system_sort_themes() string callback with an inline
 * static closure.
 *
 * @see https://www.drupal.org/node/3571172
 * @see https://www.drupal.org/node/3566774
 */
class SystemSortThemesRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated system_sort_themes() string callback with an inline static closure',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
uasort($theme_groups['installed'], 'system_sort_themes');
CODE_BEFORE,
                    <<<'CODE_AFTER'
uasort($theme_groups['installed'], static function ($a, $b) {
    if ($a->is_default) {
        return -1;
    }
    if ($b->is_default) {
        return 1;
    }
    return strcasecmp($a->info['name'], $b->info['name']);
});
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /** @param FuncCall $node */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node, 'uasort')) {
            return null;
        }

        if (count($node->args) < 2) {
            return null;
        }

        $secondArg = $node->args[1];
        if (!$secondArg instanceof Arg) {
            return null;
        }

        $callbackValue = $secondArg->value;
        if (!$callbackValue instanceof String_ || $callbackValue->value !== 'system_sort_themes') {
            return null;
        }

        $varA = new Variable('a');
        $varB = new Variable('b');

        $ifADefault = new If_(
            new PropertyFetch($varA, 'is_default'),
            ['stmts' => [new Return_(new UnaryMinus(new Int_(1)))]]
        );

        $ifBDefault = new If_(
            new PropertyFetch($varB, 'is_default'),
            ['stmts' => [new Return_(new Int_(1))]]
        );

        $returnStmt = new Return_(
            new FuncCall(
                new Node\Name('strcasecmp'),
                [
                    new Arg(new ArrayDimFetch(new PropertyFetch($varA, 'info'), new String_('name'))),
                    new Arg(new ArrayDimFetch(new PropertyFetch($varB, 'info'), new String_('name'))),
                ]
            )
        );

        $closure = new Closure([
            'static' => true,
            'params' => [new Param($varA), new Param($varB)],
            'stmts' => [$ifADefault, $ifBDefault, $returnStmt],
        ]);

        $node->args[1] = new Arg($closure);

        return $node;
    }
}
