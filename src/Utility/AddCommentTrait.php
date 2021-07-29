<?php

namespace DrupalRector\Utility;

use PhpParser\Comment;
use PhpParser\Node;
use Rector\NodeTypeResolver\Node\AttributeKey;

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
        elseif ($node->hasAttribute(AttributeKey::PARENT_NODE)) {
            $parent_node = $node->getAttribute(AttributeKey::PARENT_NODE);

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
    protected function addDrupalRectorComment(Node $node, $comment) {
        // Referencing the `parameterProvider` property in this way isn't a
        // great idea since we are assuming the property exists, but it does in
        // `AbstractRector` which all of our rules extend in some form or
        // another.
        if ($this->parameterProvider->provideParameter('drupal_rector_notices_as_comments')) {
            $comment_with_wrapper = "// TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes." . PHP_EOL
                . "// $comment";

            $statement_node = $this->getClosestStatementNode($node);

            if (!is_null($statement_node)) {
                $comments = $statement_node->getComments();
                $comments[] = new Comment($comment_with_wrapper);

                $statement_node->setAttribute(AttributeKey::COMMENTS, $comments);
            }
        }
    }

}
