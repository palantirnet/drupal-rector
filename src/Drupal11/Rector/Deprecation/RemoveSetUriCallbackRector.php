<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitor;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes deprecated EntityTypeInterface::setUriCallback() calls.
 *
 * Deprecated in drupal:11.4.0 and removed in drupal:13.0.0.
 * Standalone statement calls are deleted entirely. Mid-chain calls are
 * removed while preserving the rest of the chain.
 *
 * @see https://www.drupal.org/node/2667040
 */
final class RemoveSetUriCallbackRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove deprecated EntityTypeInterface::setUriCallback() calls',
            [
                new CodeSample(
                    '$entity_type->setUriCallback(\'my_entity_uri\');',
                    ''
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Expression::class, MethodCall::class];
    }

    public function refactor(Node $node): int|Node|null
    {
        if ($node instanceof Expression) {
            if ($node->expr instanceof MethodCall
                && $this->isName($node->expr->name, 'setUriCallback')
            ) {
                return NodeVisitor::REMOVE_NODE;
            }
            return null;
        }

        // Fluent chain: $entity_type->setUriCallback('func')->someOtherMethod()
        if ($node instanceof MethodCall) {
            if ($node->var instanceof MethodCall
                && $this->isName($node->var->name, 'setUriCallback')
            ) {
                $node->var = $node->var->var;
                return $node;
            }
        }

        return null;
    }
}
