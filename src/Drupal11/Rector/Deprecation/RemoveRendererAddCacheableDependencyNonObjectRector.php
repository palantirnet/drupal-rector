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
 * Removes RendererInterface::addCacheableDependency() calls whose dependency
 * argument is provably not an object (bool, int, float, string, null, array).
 *
 * Passing such values triggers a deprecation in drupal:11.3.0 and will throw
 * in drupal:13.0.0, and silently sets max-age 0 on the render array, making
 * pages uncacheable. The call has no useful effect and can be dropped.
 *
 * @see https://www.drupal.org/node/3525388
 * @see https://www.drupal.org/node/3525389
 */
class RemoveRendererAddCacheableDependencyNonObjectRector extends AbstractRector
{
    public const PHPSTAN_MESSAGES = [
        "Calling Drupal\\Core\\Render\\Renderer::addCacheableDependency() with an object that doesn't implement Drupal\\Core\\Cache\\CacheableDependencyInterface is deprecated in drupal:11.3.0 and will throw an error in drupal:13.0.0. See https://www.drupal.org/node/3525389",
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove RendererInterface::addCacheableDependency() calls where the dependency argument cannot implement CacheableDependencyInterface (bool, int, float, string, null, array). Such calls silently make pages uncacheable and are deprecated in drupal:11.3.0.',
            [
                new CodeSample(
                    '$this->renderer->addCacheableDependency($build, false);',
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
        if (!$this->isName($methodCall->name, 'addCacheableDependency')) {
            return null;
        }

        // RendererInterface::addCacheableDependency() takes exactly 2 arguments
        // (array &$elements, $dependency). The 1-argument variant on
        // RefinableCacheableDependencyInterface/BubbleableMetadata is unrelated.
        if (count($methodCall->args) !== 2) {
            return null;
        }

        if (!$this->isObjectType($methodCall->var, new ObjectType('Drupal\Core\Render\RendererInterface'))) {
            return null;
        }

        // Only remove when PHPStan can prove the dependency is not an object.
        $dependencyType = $this->getType($methodCall->args[1]->value);
        if (!$dependencyType->isObject()->no()) {
            return null;
        }

        return NodeVisitor::REMOVE_NODE;
    }
}
