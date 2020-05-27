<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  /* @var \Drupal\node\Entity\Node $node */
  $node = \Drupal::service('entity_type.manager')->getStorage('node')->load(123);
}

/**
 * An example using all of the arguments.
 */
function all_arguments() {
  /* @var \Drupal\node\Entity\Node $node */
  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // A ternary operator is used here to keep the conditional contained within this part of the expression. Consider wrapping this statement in an `if / else` statement.
  $node = TRUE ? \Drupal::service('entity_type.manager')->getStorage('node')->resetCache([123])->load(123) : \Drupal::service('entity_type.manager')->getStorage('node')->load(123);
}

/**
 * An example using all of the arguments as variables.
 */
function all_arguments_as_variables() {
  $entity_id = 123;
  $reset = TRUE;

  /* @var \Drupal\node\Entity\Node $node */
  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // A ternary operator is used here to keep the conditional contained within this part of the expression. Consider wrapping this statement in an `if / else` statement.
  $node = $reset ? \Drupal::service('entity_type.manager')->getStorage('node')->resetCache([$entity_id])->load($entity_id) : \Drupal::service('entity_type.manager')->getStorage('node')->load($entity_id);
}
