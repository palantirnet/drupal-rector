<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes deprecated $form['#submit'][] = 'automated_cron_settings_submit' assignments.
 *
 * Deprecated in drupal:11.4.0, removed in drupal:13.0.0.
 * Config saving is now handled automatically via #config_target on the interval element.
 *
 * @see https://www.drupal.org/node/3566768
 */
final class RemoveAutomatedCronSubmitHandlerRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Stmt\Expression::class];
    }

    public function refactor(Node $node): mixed
    {
        assert($node instanceof Node\Stmt\Expression);

        if (!$node->expr instanceof Node\Expr\Assign) {
            return null;
        }

        $assign = $node->expr;

        if (!$assign->expr instanceof Node\Scalar\String_) {
            return null;
        }

        if ($assign->expr->value !== 'automated_cron_settings_submit') {
            return null;
        }

        // Must be an array append ([] = ...) with no explicit index.
        if (!$assign->var instanceof Node\Expr\ArrayDimFetch) {
            return null;
        }

        if ($assign->var->dim !== null) {
            return null;
        }

        return NodeVisitor::REMOVE_NODE;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition("Removes deprecated \$form['#submit'][] = 'automated_cron_settings_submit' handler assignments (drupal:11.4.0)", [
            new CodeSample(
                "\$form['#submit'][] = 'automated_cron_settings_submit';",
                ''
            ),
        ]);
    }
}
