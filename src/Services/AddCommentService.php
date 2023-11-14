<?php

declare(strict_types=1);

namespace DrupalRector\Services;

use PhpParser\Comment;
use PhpParser\Node;
use Rector\NodeTypeResolver\Node\AttributeKey;

/**
 * Provides an easy way to add a comment to a statement.
 */
class AddCommentService
{
    protected bool $noticesAsComments = false;

    /**
     * @param bool $noticesAsComments
     */
    public function __construct(bool $noticesAsComments = true)
    {
        $this->noticesAsComments = $noticesAsComments;
    }

    /**
     * Add a comment to the parent statement.
     *
     * @param Node\Stmt\Expression $node
     * @param string               $comment
     *
     * @return void
     */
    public function addDrupalRectorComment(Node $node, string $comment)
    {
        // Referencing the `parameterProvider` property in this way isn't a
        // great idea since we are assuming the property exists, but it does in
        // `AbstractRector` which all of our rules extend in some form or
        // another.
        if ($this->noticesAsComments) {
            $comment_with_wrapper = "// TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.".PHP_EOL
                ."// $comment";

            $comments = $node->getComments();
            $comments[] = new Comment($comment_with_wrapper);

            $node->setAttribute(AttributeKey::COMMENTS, $comments);
        }
    }
}
