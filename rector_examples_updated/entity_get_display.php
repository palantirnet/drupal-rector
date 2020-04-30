<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  $form_display = \Drupal::service('entity_display.repository')->getViewDisplay('node', 'page', 'default');
}

/**
 * An example using variables as the arguments.
 */
function arguments_as_variables() {
  $entity_type = 'node';
  $bundle = 'page';
  $form_mode = 'default';

  $form_display = $form_display = \Drupal::service('entity_display.repository')->getViewDisplay($entity_type, $bundle, $form_mode);
}
