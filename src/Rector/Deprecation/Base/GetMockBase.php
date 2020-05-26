<?php

namespace DrupalRector\Rector\Deprecation\Base;

use PhpParser\Node;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Core\Rector\AbstractRector;

/**
 * Replaces deprecated getMock() calls in classes.
 *
 * See https://www.drupal.org/node/2907725 for change record.
 *
 * What is covered:
 * - Checks the class being extended.
 */
abstract class GetMockBase extends AbstractRector
{

  /**
   * The fully qualified base class our classes are extending.
   *
   * @var string
   */
  protected $baseClassBeingExtended;

  /**
   * @inheritdoc
   */
  public function getNodeTypes(): array
  {
    return [
      Node\Expr\MethodCall::class,
    ];
  }

  /**
   * @inheritdoc
   */
  public function refactor(Node $node): ?Node
  {
    /* @var Node\Expr\MethodCall $node */
    if ($this->getName($node->name) === 'getMock' && ($node->var instanceof Node\Expr\Variable) && $this->getName($node->var) === 'this' && $node->hasAttribute(AttributeKey::PARENT_CLASS_NAME) && $node->getAttribute(AttributeKey::PARENT_CLASS_NAME) === $this->baseClassBeingExtended) {

      // Build the arguments.
      $method_arguments = $node->args;

      // Get the updated method name.
      $method_name = new Node\Identifier('createMock');

      $node = new Node\Expr\MethodCall($node->var, $method_name, $method_arguments);

      return $node;
    }

    return null;
  }
}
