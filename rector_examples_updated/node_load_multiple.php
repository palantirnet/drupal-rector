<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  $nodes = \Drupal::service('entity_type.manager')->getStorage('node')->loadMultiple([123, 456]);
}

/**
 * An example using all of the arguments.
 */
function all_arguments() {
  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // A ternary operator is used here to keep the conditional contained within this part of the expression. Consider wrapping this statement in an `if / else` statement.
  $nodes = TRUE ? \Drupal::service('entity_type.manager')->getStorage('node')->resetCache([123, 456])->loadMultiple([123, 456]) : \Drupal::service('entity_type.manager')->getStorage('node')->loadMultiple([123, 456]);
}

/**
 * An example using a variable for the argument.
 */
function all_arguments_as_variables() {
  $node_ids = [123, 456];
  $reset = TRUE;
  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // A ternary operator is used here to keep the conditional contained within this part of the expression. Consider wrapping this statement in an `if / else` statement.
  $nodes = $reset ? \Drupal::service('entity_type.manager')->getStorage('node')->resetCache($node_ids)->loadMultiple($node_ids) : \Drupal::service('entity_type.manager')->getStorage('node')->loadMultiple($node_ids);
}
