<?php

namespace DrupalRector\Rector\Deprecation\Base;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;

/**
 * Replaced deprecated entity_load() calls.
 *
 * See https://www.drupal.org/node/2266845 for change record.
 *
 * What is covered:
 * - Static replacement
 * - The reset parameter is handled with a ternary operator
 *
 * Improvement opportunities
 * - Dependency injection
 */
abstract class EntityLoadBase extends AbstractRector
{

  /**
   * The entity type to load.
   *
   * If this is not set, we assume we need to get it passed to entity_load().
   *
   * @var string $entityType
   */
  protected $entityType;

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

    // If we set an entity type, we are using something like node_load() which does not pass in the entity type.
    if (!is_null($this->entityType)) {
      // Create an argument, like that which is passed by entity_load().
      $entity_type = new Node\Arg(new Node\Scalar\String_($this->entityType));

      $method_name = $this->entityType . '_load';

      $argument_offset = 0;
    }
    // If we do not set entityType, we are using entity_load().
    else {
      /* @var Node\Arg $entity_type. */
      $entity_type = $node->args[0];

      $method_name = 'entity_load';

      // Since we have one more argument, all the array keys are one greater.
      $argument_offset = 1;
    }

    /** @var Node\Expr\FuncCall $node */
    if ($this->getName($node->name) === $method_name) {

      /* @var Node\Arg $entity_id. */
      $entity_id = $node->args[0 + $argument_offset];

      $name = new Node\Name\FullyQualified('Drupal');

      $call = new Node\Identifier('service');

      $entity_type_manager_args = [
        new Node\Arg(new Node\Scalar\String_('entity_type.manager')),
      ];

      $var = new Node\Expr\StaticCall($name, $call, $entity_type_manager_args);

      $getStorage_method_name = new Node\Identifier('getStorage');

      // Start to build the new node.
      $getStorage_node = new Node\Expr\MethodCall($var, $getStorage_method_name, [$entity_type]);

      // Create the simple version of the entity load.
      $load_method_name = new Node\Identifier('load');

      $new_node = new Node\Expr\MethodCall($getStorage_node, $load_method_name, [$entity_id]);

      // We need to account for the `reset` option which adds a method to the chain.
      // We will replace the original method with a ternary to evaluate and provide both options.
      if (count($node->args) == (2 + $argument_offset)) {
        /* @var Node\Arg $reset_flag. */
        $reset_flag = $node->args[1 + $argument_offset];

        $resetCache_method_name = new Node\Identifier('resetCache');

        $reset_args = [
          // This creates a new argument that wraps the entity ID in an array.
          new Node\Arg(new Node\Expr\Array_([new Node\Expr\ArrayItem($entity_id->value)])),
        ];

        $entity_load_reset_node = new Node\Expr\MethodCall($getStorage_node, $resetCache_method_name, $reset_args);

        $entity_load_reset_node = new Node\Expr\MethodCall($entity_load_reset_node, $load_method_name, [$entity_id]);

        // Replace the new_node with a ternary.
        $new_node = new Node\Expr\Ternary($reset_flag->value, $entity_load_reset_node, $new_node);
      }

      return $new_node;
    }

    return null;
  }
}
