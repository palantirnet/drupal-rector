<?php

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Renames deprecated Drupal::services().
 *
 * What is covered:
 *   - See $map below.
 */
final class RenameDrupalServiceRector extends AbstractRector {

  /**
   * A directory of old names mapped onto new names.
   */
  protected $map = array(
       // See https://www.drupal.org/node/3092086
      'path.alias_manager' => 'path_alias.manager',
      'path.alias_whitelist' => 'path_alias.whitelist',
      'path_subscriber' => 'path_alias.subscriber',
      'path_processor_alias'=> 'path_alias.path_processor'
    );

  /**
   * @inheritdoc
   */
  public function getDefinition(): RectorDefinition {
    return new RectorDefinition('Renames the IDs in Drupal::service() calls',[
      new CodeSample(
        <<<'CODE_BEFORE'
\Drupal::service('old')->foo();
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
\Drupal::service('bar')->foo();
CODE_AFTER
      )
    ]);
  }

  /**
   * @inheritdoc
   */
  public function getNodeTypes(): array {
    return [
      Node\Expr\StaticCall::class
    ];
  }

  /**
   * @inheritdoc
   */
  public function refactor(Node $node): ?Node {

    if ($node instanceof Node\Expr\StaticCall) {
      /** @var Node\Expr\StaticCall $node */
      if ($node->name instanceof Node\Identifier && (string) $node->class === 'Drupal' && (string) $node->name === 'service') {
        if ($node->args[0]->value instanceof Node\Scalar\String_) {
          $service = $node->args[0]->value->value;
          if ($new = $this->map[$service]) {
            return new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'service', [new Node\Arg(new Node\Scalar\String_($new))]);
          }
        }
      }
    }

    return null;
  }
}
