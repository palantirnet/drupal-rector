<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes deprecated $settings['state_cache'] assignments.
 *
 * Deprecated in drupal:11.0.0 — state caching is now permanently enabled
 * and the setting has no effect.
 *
 * @see https://www.drupal.org/node/3436954
 * @see https://www.drupal.org/node/2575105
 */
final class RemoveStateCacheSettingRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Stmt\Expression::class];
    }

    /** @return NodeVisitor::REMOVE_NODE|null */
    public function refactor(Node $node): mixed
    {
        assert($node instanceof Node\Stmt\Expression);

        if (!$node->expr instanceof Node\Expr\Assign) {
            return null;
        }

        $assign = $node->expr;

        if (!$assign->var instanceof Node\Expr\ArrayDimFetch) {
            return null;
        }

        $arrayDimFetch = $assign->var;

        if (!$this->isName($arrayDimFetch->var, 'settings')) {
            return null;
        }

        if (!$arrayDimFetch->dim instanceof Node\Scalar\String_) {
            return null;
        }

        if ($arrayDimFetch->dim->value !== 'state_cache') {
            return null;
        }

        return NodeVisitor::REMOVE_NODE;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition("Removes deprecated \$settings['state_cache'] assignments. State caching is permanently enabled since drupal:11.0.0 and the setting has no effect", [
            new CodeSample(
                "\$settings['state_cache'] = TRUE;",
                ''
            ),
        ]);
    }
}
