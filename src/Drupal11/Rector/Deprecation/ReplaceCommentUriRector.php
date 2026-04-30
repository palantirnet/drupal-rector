<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated comment_uri($comment) calls with $comment->permalink().
 *
 * Deprecated in drupal:11.3.0, removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/2010202
 */
final class ReplaceCommentUriRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Expr\FuncCall::class];
    }

    public function refactor(Node $node): mixed
    {
        assert($node instanceof Node\Expr\FuncCall);

        if (!$this->isName($node, 'comment_uri')) {
            return null;
        }

        if (count($node->args) < 1) {
            return null;
        }

        return new Node\Expr\MethodCall(
            $node->args[0]->value,
            new Node\Identifier('permalink')
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated comment_uri($comment) calls with $comment->permalink() (drupal:11.3.0)', [
            new CodeSample(
                '$url = comment_uri($comment);',
                '$url = $comment->permalink();'
            ),
        ]);
    }
}
