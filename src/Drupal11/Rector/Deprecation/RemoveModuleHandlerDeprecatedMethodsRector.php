<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitor;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes deprecated ModuleHandlerInterface::writeCache() calls and replaces
 * ModuleHandlerInterface::getHookInfo() with [].
 *
 * Both are deprecated in drupal:11.1.0 and removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3442009
 * @see https://www.drupal.org/node/3368812
 */
final class RemoveModuleHandlerDeprecatedMethodsRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove deprecated ModuleHandlerInterface::writeCache() calls and replace getHookInfo() with []',
            [
                new CodeSample(
                    '$this->moduleHandler->writeCache();',
                    ''
                ),
                new CodeSample(
                    '$hookInfo = $this->moduleHandler->getHookInfo();',
                    '$hookInfo = [];'
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
        if ($node instanceof Expression && $node->expr instanceof MethodCall) {
            $call = $node->expr;
            if ($this->isModuleHandlerMethodCall($call, 'writeCache')
                || $this->isModuleHandlerMethodCall($call, 'getHookInfo')
            ) {
                return NodeVisitor::REMOVE_NODE;
            }
        }

        if ($node instanceof MethodCall
            && $this->isModuleHandlerMethodCall($node, 'getHookInfo')
        ) {
            return new Array_([]);
        }

        return null;
    }

    private function isModuleHandlerMethodCall(MethodCall $call, string $methodName): bool
    {
        return $this->isName($call->name, $methodName)
            && $this->isObjectType(
                $call->var,
                new ObjectType('Drupal\Core\Extension\ModuleHandlerInterface')
            );
    }
}
