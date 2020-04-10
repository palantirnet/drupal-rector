<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\TraitsByClassHelperTrait;
use PhpParser\Node;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated file_prepare_directory() calls.
 *
 * See https://www.drupal.org/node/3006851 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class FilePrepareDirectoryRector extends AbstractRector
{
    use TraitsByClassHelperTrait;

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated drupal_set_message() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
$result = file_prepare_directory($directory, $options);
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$result = \Drupal::service('file_system')->prepareDirectory($directory, $options);
CODE_AFTER
            )
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Expr\FuncCall $node */
        if ($node->name instanceof Node\Name && 'file_prepare_directory' === (string) $node->name) {

            // This creates a service call like `\Drupal::service('file_system').
            // TODO use dependency injection.
            $file_system_service = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'service', [new Node\Arg(new Node\Scalar\String_('file_system'))]);

            $method_name = 'prepareDirectory';

            $method = new Node\Identifier($method_name);

            $node = new Node\Expr\MethodCall($file_system_service, $method, $node->args);
        }

        return $node;
    }
}
