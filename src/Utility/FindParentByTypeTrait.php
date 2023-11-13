<?php

declare(strict_types=1);

namespace DrupalRector\Utility;

use PhpParser\Node;

trait FindParentByTypeTrait
{
    /**
     * @template T of Node
     *
     * @param Node            $node
     * @param class-string<T> $type
     *
     * @return T|null
     */
    public function findParentType(Node $node, string $type): ?Node
    {
        $parentNode = $node->getAttribute('parent');

        while ($parentNode instanceof Node) {
            if ($parentNode instanceof $type) {
                return $parentNode;
            }

            $parentNode = $parentNode->getAttribute('parent');
        }

        return null;
    }
}
