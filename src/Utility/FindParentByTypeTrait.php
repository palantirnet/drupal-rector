<?php

namespace DrupalRector\Utility;

use PhpParser\Node;

trait FindParentByTypeTrait
{
    /**
     * @param Node $node
     * @param class-string<T> $type
     * @return Node|null
     * @template T of Node
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
