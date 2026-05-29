<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes deprecated CacheTagChecksumCount and CacheTagIsValidCount entries from assertMetrics() calls.
 *
 * @see https://www.drupal.org/node/3511123
 * @see https://www.drupal.org/node/3511149
 */
class RemoveCacheTagChecksumAssertionsRector extends AbstractRector
{
    private const DEPRECATED_KEYS = [
        'CacheTagChecksumCount',
        'CacheTagIsValidCount',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove deprecated CacheTagChecksumCount and CacheTagIsValidCount entries from assertMetrics() calls (deprecated in drupal:11.2.0, removed in drupal:12.0.0, no replacement).',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
$this->assertMetrics([
    'CacheGetCount' => 5,
    'CacheTagChecksumCount' => 38,
    'CacheTagIsValidCount' => 43,
    'CacheTagInvalidationCount' => 0,
], $performance_data);
CODE_BEFORE,
                    <<<'CODE_AFTER'
$this->assertMetrics([
    'CacheGetCount' => 5,
    'CacheTagInvalidationCount' => 0,
], $performance_data);
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /** @param MethodCall $node */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->name, 'assertMetrics')) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Tests\PerformanceTestTrait'))) {
            return null;
        }

        if (!isset($node->args[0])) {
            return null;
        }

        $firstArg = $node->args[0]->value;
        if (!$firstArg instanceof Array_) {
            return null;
        }

        $changed = false;
        $newItems = [];
        foreach ($firstArg->items as $item) {
            $key = $item->key;
            if ($key instanceof String_ && in_array($key->value, self::DEPRECATED_KEYS, true)) {
                $changed = true;
                continue;
            }
            $newItems[] = $item;
        }

        if (!$changed) {
            return null;
        }

        $firstArg->items = $newItems;

        return $node;
    }
}
