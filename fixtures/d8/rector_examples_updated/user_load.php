<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  /* @var \Drupal\user\Entity\User $user */
  $user = \Drupal::service('entity_type.manager')->getStorage('user')->load(123);
}

/**
 * An example using all of the arguments.
 */
function all_arguments() {
  /* @var \Drupal\user\Entity\User $user */
  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // A ternary operator is used here to keep the conditional contained within this part of the expression. Consider wrapping this statement in an `if / else` statement.
  $user = TRUE ? \Drupal::service('entity_type.manager')->getStorage('user')->resetCache([123])->load(123) : \Drupal::service('entity_type.manager')->getStorage('user')->load(123);
}

/**
 * An example using all of the arguments as variables.
 */
function all_arguments_as_variables() {
  $entity_id = 123;
  $reset = TRUE;

  /* @var \Drupal\user\Entity\User $user */
  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // A ternary operator is used here to keep the conditional contained within this part of the expression. Consider wrapping this statement in an `if / else` statement.
  $user = $reset ? \Drupal::service('entity_type.manager')->getStorage('user')->resetCache([$entity_id])->load($entity_id) : \Drupal::service('entity_type.manager')->getStorage('user')->load($entity_id);
}
