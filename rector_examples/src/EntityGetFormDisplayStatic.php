<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class EntityGetFormDisplayStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    $form_display = entity_get_form_display('node', 'page', 'default');
  }

  /**
   * An example using variables as the arguments.
   */
  public function arguments_as_variables() {
    $entity_type = 'node';
    $bundle = 'page';
    $form_mode = 'default';

    $form_display = entity_get_form_display($entity_type, $bundle, $form_mode);
  }

}
