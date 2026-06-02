<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitor;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes calls to AliasManager::setCacheKey() and AliasManager::writeCache().
 *
 * Both methods are deprecated in drupal:11.3.0 and removed in drupal:13.0.0
 * with no replacement. They became no-ops when the path alias preload cache
 * was replaced by a Fiber-based bulk-lookup strategy, so callers can simply
 * drop the call.
 *
 * @see https://www.drupal.org/node/3496369
 * @see https://www.drupal.org/node/3532412
 */
class RemoveAliasManagerCacheMethodCallsRector extends AbstractRector
{
    public const PHPSTAN_MESSAGES = [
        'Call to deprecated method setCacheKey() of class Drupal\path_alias\AliasManager. Deprecated in drupal:11.3.0 and is removed from drupal:13.0.0. There is no replacement.',
        'Call to deprecated method writeCache() of class Drupal\path_alias\AliasManager. Deprecated in drupal:11.3.0 and is removed from drupal:13.0.0. There is no replacement.',
    ];

    private const TARGET_METHODS = ['setCacheKey', 'writeCache'];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove calls to AliasManager::setCacheKey() and AliasManager::writeCache(), deprecated in drupal:11.3.0 and removed in drupal:13.0.0 with no replacement.',
            [
                new CodeSample(
                    '$this->aliasManager->setCacheKey($path);',
                    ''
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Expression::class];
    }

    public function refactor(Node $node): ?int
    {
        assert($node instanceof Expression);
        if (!$node->expr instanceof MethodCall) {
            return null;
        }

        $methodCall = $node->expr;
        if (!$this->isNames($methodCall->name, self::TARGET_METHODS)) {
            return null;
        }

        if (
            !$this->isObjectType($methodCall->var, new ObjectType('Drupal\path_alias\AliasManager'))
            && !$this->isObjectType($methodCall->var, new ObjectType('Drupal\path_alias\AliasManagerInterface'))
        ) {
            return null;
        }

        return NodeVisitor::REMOVE_NODE;
    }
}
