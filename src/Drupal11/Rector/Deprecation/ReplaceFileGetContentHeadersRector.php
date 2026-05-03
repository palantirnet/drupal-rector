<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated file_get_content_headers($file) with $file->getDownloadHeaders().
 *
 * Deprecated in drupal:11.2.0, removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3494126
 */
final class ReplaceFileGetContentHeadersRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Expr\FuncCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\FuncCall);

        if (!$this->isName($node, 'file_get_content_headers')) {
            return null;
        }

        if (count($node->args) !== 1) {
            return null;
        }

        return new Node\Expr\MethodCall(
            $node->args[0]->value,
            new Node\Identifier('getDownloadHeaders')
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated file_get_content_headers($file) with $file->getDownloadHeaders() (drupal:11.2.0)', [
            new CodeSample(
                '$headers = file_get_content_headers($file);',
                '$headers = $file->getDownloadHeaders();'
            ),
        ]);
    }
}
