<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces statement-level hide() and show() calls with direct #printed assignment.
 *
 * The global hide() and show() functions are deprecated in drupal:11.4.0 and
 * removed in drupal:13.0.0. They are thin wrappers around setting
 * $element['#printed'] = TRUE/FALSE on the render array. Expression-context
 * uses (where the return value is captured) are intentionally skipped because
 * the original returns the element while the rewrite would not.
 *
 * @see https://www.drupal.org/node/2258355
 * @see https://www.drupal.org/node/3261271
 */
final class ReplaceHideShowWithPrintedRector extends AbstractRector
{
    /** @var array<string, bool> */
    private const FUNCTION_TO_PRINTED_VALUE = [
        'hide' => true,
        'show' => false,
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated global hide() and show() functions with direct #printed property assignment on the render element.',
            [
                new CodeSample(
                    'hide($element);',
                    "\$element['#printed'] = TRUE;",
                ),
                new CodeSample(
                    'show($element);',
                    "\$element['#printed'] = FALSE;",
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Expression::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Expression);

        if (!$node->expr instanceof FuncCall) {
            return null;
        }

        $call = $node->expr;

        if (!$this->isNames($call->name, ['hide', 'show'])) {
            return null;
        }

        if (count($call->args) !== 1 || !$call->args[0] instanceof Arg) {
            return null;
        }

        $funcName = $this->getName($call->name);

        if ($funcName === null || !isset(self::FUNCTION_TO_PRINTED_VALUE[$funcName])) {
            return null;
        }

        $value = self::FUNCTION_TO_PRINTED_VALUE[$funcName]
            ? $this->nodeFactory->createTrue()
            : $this->nodeFactory->createFalse();

        $node->expr = new Assign(
            new ArrayDimFetch($call->args[0]->value, new String_('#printed')),
            $value,
        );

        return $node;
    }
}
