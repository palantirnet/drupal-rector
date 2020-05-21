<?php

namespace DrupalRector\Utility;

use PhpParser\Comment;
use PhpParser\Node;

/**
 * Provides an easy way to add a comment to a statement.
 */
trait AddCommentTrait
{
    /**
     * Get the closest statement for the node.
     *
     * @param Node $node
     *
     * @return Node|NULL
     */
    protected function getClosestStatementNode(Node $node): ?Node {
        $statement_node = NULL;

        if ($node instanceof Node\Stmt) {
            $statement_node = $node;
        }
        elseif ($node->hasAttribute('parentNode')) {
            $parent_node = $node->getAttribute('parentNode');

            $statement_node = $this->getClosestStatementNode($parent_node);
        }

        return $statement_node;
    }

    /**
     * Add a comment to the parent statement.
     *
     * @param Node $node
     * @param string $comment
     */
    protected function addComment(Node $node, $comment) {
        $statement_node = $this->getClosestStatementNode($node);

        if (!is_null($statement_node)) {
            $comments = $statement_node->getComments();
            $comments[] = new Comment($comment);

            $statement_node->setAttribute('comments', $comments);
        }
    }

}
