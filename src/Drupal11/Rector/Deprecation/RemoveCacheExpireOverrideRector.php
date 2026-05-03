<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes overridden cacheExpire() methods from CachePluginBase subclasses.
 *
 * CachePluginBase::cacheExpire() is deprecated in drupal:11.4.0 and removed
 * in drupal:13.0.0. Cache expiration is now configured via cacheSetMaxAge().
 *
 * @see https://www.drupal.org/node/3576556
 */
final class RemoveCacheExpireOverrideRector extends AbstractRector
{
    private const CACHE_PLUGIN_BASE_FQCN = 'Drupal\views\Plugin\views\cache\CachePluginBase';

    private const PARENT_SHORT_NAMES = ['CachePluginBase', 'Time', 'Tag', 'None'];

    private const PARENT_FQCNS = [
        'Drupal\views\Plugin\views\cache\CachePluginBase',
        'Drupal\views\Plugin\views\cache\Time',
        'Drupal\views\Plugin\views\cache\Tag',
        'Drupal\views\Plugin\views\cache\None',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove deprecated cacheExpire() overrides from Views CachePluginBase subclasses',
            [
                new CodeSample(
                    'class MyCache extends CachePluginBase { protected function cacheExpire($type) { return 0; } }',
                    'class MyCache extends CachePluginBase { }'
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Class_);
        if (!$this->isCachePluginBaseSubclass($node)) {
            return null;
        }

        $changed = false;
        foreach ($node->stmts as $key => $stmt) {
            if ($stmt instanceof ClassMethod && $this->isName($stmt, 'cacheExpire')) {
                unset($node->stmts[$key]);
                $changed = true;
            }
        }

        return $changed ? $node : null;
    }

    private function isCachePluginBaseSubclass(Class_ $node): bool
    {
        if ($node->extends === null) {
            return false;
        }

        $parentName = $node->extends->toString();

        // Match fully-qualified names (Rector resolves `use` aliases before calling refactor).
        foreach (self::PARENT_FQCNS as $fqcn) {
            if ($parentName === $fqcn) {
                return true;
            }
        }

        // Match unqualified short names (no `use` statement, global-namespace usage).
        if (!str_contains($parentName, '\\')) {
            foreach (self::PARENT_SHORT_NAMES as $short) {
                if ($parentName === $short) {
                    return true;
                }
            }
        }

        try {
            if ($this->isObjectType($node->extends, new ObjectType(self::CACHE_PLUGIN_BASE_FQCN))) {
                return true;
            }
        } catch (\Throwable) {
        }

        return false;
    }
}
