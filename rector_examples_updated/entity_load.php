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
  $node = TRUE ? \Drupal::service('entity_type.manager')->getStorage('node')->resetCache([123])->load(123) : \Drupal::service('entity_type.manager')->getStorage('node')->load(123);
}

/**
 * An example using all of the arguments.
 */
function all_arguments_as_variables() {
  $entity_type = 'node';
  $entity_id = 123;
  $reset = TRUE;

  /* @var \Drupal\node\Entity\Node $node */
  $node = $reset ? \Drupal::service('entity_type.manager')->getStorage($entity_type)->resetCache([$entity_id])->load($entity_id) : \Drupal::service('entity_type.manager')->getStorage($entity_type)->load($entity_id);
}
