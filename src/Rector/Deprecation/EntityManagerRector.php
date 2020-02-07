<?php

namespace Drupal8Rector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated \Drupal::entityManager() calls.
 *
 * See https://www.drupal.org/node/2549139 for change record.
 */
final class EntityManagerRector extends AbstractRector {

  /**
   * @inheritdoc
   */
  public function getDefinition(): RectorDefinition {
    return new RectorDefinition('Fixes deprecated \Drupal::entityManager() calls',[
      new CodeSample(
        <<<'CODE_BEFORE'
$entity_manager = \Drupal::entityManager();
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
$entity_manager = \Drupal::entityTypeManager();
CODE_AFTER
      )
    ]);
  }

  /**
   * @inheritdoc
   */
  public function getNodeTypes(): array {
    return [
      Node\Expr\StaticCall::class,
//      Node\Expr\FuncCall::class,
//      Node\Expr\MethodCall::class,
    ];
  }

  /**
   * @inheritdoc
   */
  public function refactor(Node $node): ?Node {

    if ($node instanceof Node\Expr\StaticCall) {
      /** @var Node\Expr\StaticCall $node */
      if ($node->name instanceof Node\Identifier && (string) $node->class === 'Drupal' && (string) $node->name === 'entityManager') {
        $node = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'entityTypeManager', $node->args, $node->getAttributes());
      }
    }

    return $node;
  }
}
