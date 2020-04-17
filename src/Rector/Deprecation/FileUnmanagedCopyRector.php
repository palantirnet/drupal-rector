<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\TraitsByClassHelperTrait;
use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated file_unmanaged_copy() calls.
 *
 * See https://api.drupal.org/api/drupal/core%21includes%21file.inc/function/file_unmanaged_copy/8.7.x.
 *
 * What is covered:
 * - File System Service used
 *
 * Improvement opportunities
 * - Dependency Injection
 */
final class FileUnmanagedCopyRector extends AbstractRector {
  use TraitsByClassHelperTrait;

  /**
   * @inheritdoc
   */
  public function getDefinition(): RectorDefinition {
    return new RectorDefinition('Fixes deprecated file_unmanaged_copy() calls', [
      new CodeSample(
        <<<'CODE_BEFORE'
file_unmanaged_copy($source, $destination, $replace);
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
\Drupal::service('file_system')->copy($source, $destination, $replace);
CODE_AFTER
      ),
    ]);
  }

  /**
   * @inheritdoc
   */
  public function getNodeTypes(): array {
    return [
      Node\Expr\FuncCall::class,
    ];
  }

  /**
   * @inheritdoc
   */
  public function refactor(Node $node): ?Node {
    if ($node->name instanceof Node\Name && 'file_unmanaged_copy' === (string) $node->name) {
      $file_system_service = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'service', [new Node\Arg(new Node\Scalar\String_('file_system'))]);

      $method_name = 'copy';

      $method = new Node\Identifier($method_name);

      $node = new Node\Expr\MethodCall($file_system_service, $method, $node->args);

      return $node;

    }

    return NULL;
  }

}
