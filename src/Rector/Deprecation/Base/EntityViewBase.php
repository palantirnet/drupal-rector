<?php

namespace DrupalRector\Rector\Deprecation\Base;

use DrupalRector\Utility\AddCommentTrait;
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
abstract class EntityViewBase extends AbstractRector
{

  use AddCommentTrait;

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

    if ($this->getName($node->name) !== 'entity_view') {
      return NULL;
    }

    $name = new Node\Name\FullyQualified('Drupal');

    $entity_type_method_name = new Node\Identifier('entityTypeManager');

    $var = new Node\Expr\StaticCall($name, $entity_type_method_name);

    $getViewBuilder_method_name = new Node\Identifier('getViewBuilder');

    $entityRef = $node->args[0]->value;
    $getEntityTypeId_method_name = new Node\Identifier('getEntityTypeId');

    $entityRef_type_id = new Node\Expr\MethodCall($entityRef, $getEntityTypeId_method_name);

    $view_builder = new Node\Expr\MethodCall($var, $getViewBuilder_method_name, [$entityRef_type_id]);

    $view_method_name = new Node\Identifier('view');

    $view_args = [
      $node->args[0]->value,
      $node->args[1]->value,
    ];

    if (isset($node->args[2])) {
      $view_args[] = $node->args[2]->value;
    }

    if (isset($node->args[3])) {
      $view_args[] = $node->args[3]->value;
    }

    $view = new Node\Expr\MethodCall($view_builder, $view_method_name, $view_args);

    return $view;

  }
}
