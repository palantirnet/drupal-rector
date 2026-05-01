<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes deprecated CachePluginBase::getRowCacheKeys() and getRowId() array item values.
 *
 * Both methods are deprecated in drupal:11.4.0 and removed in drupal:13.0.0
 * with no replacement.
 *
 * @see https://www.drupal.org/node/3564958
 */
final class RemoveViewsRowCacheKeysRector extends AbstractRector
{
    private const DEPRECATED_METHODS = ['getRowCacheKeys', 'getRowId'];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove deprecated CachePluginBase::getRowCacheKeys() and getRowId() array item values',
            [
                new CodeSample(
                    "['keys' => \$cache_plugin->getRowCacheKeys(\$row), 'tags' => []]",
                    "['tags' => []]"
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Array_::class];
    }

    public function refactor(Node $node): ?Node
    {
        $modified = false;
        $newItems = [];

        foreach ($node->items as $item) {
            if (!$item instanceof ArrayItem) {
                $newItems[] = $item;
                continue;
            }
            if ($item->value instanceof MethodCall
                && $item->value->name instanceof Identifier
                && in_array($item->value->name->toString(), self::DEPRECATED_METHODS, true)
                && $this->isObjectType($item->value->var, new ObjectType('Drupal\views\Plugin\views\cache\CachePluginBase'))
            ) {
                $modified = true;
                continue;
            }
            $newItems[] = $item;
        }

        if (!$modified) {
            return null;
        }
        $node->items = $newItems;

        return $node;
    }
}
