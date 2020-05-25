<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class EntityGetDisplayStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    $view_display = entity_get_display('node', 'page', 'default');
  }

  /**
   * An example using variables as the arguments.
   */
  public function arguments_as_variables() {
    $entity_type = 'node';
    $bundle = 'page';
    $view_mode = 'default';

    $view_display = entity_get_display($entity_type, $bundle, $view_mode);
  }

}
