<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  /* @var \Drupal\file\Entity\File $file */
  $file = \Drupal::service('entity_type.manager')->getStorage('file')->load(123);
}

/**
 * An example using all of the arguments.
 */
function all_arguments() {
  /* @var \Drupal\file\Entity\File $file */
  $file = TRUE ? \Drupal::service('entity_type.manager')->getStorage('file')->resetCache([123])->load(123) : \Drupal::service('entity_type.manager')->getStorage('file')->load(123);
}

/**
 * An example using all of the arguments as variables.
 */
function all_arguments_as_variables() {
  $entity_id = 123;
  $reset = TRUE;

  /* @var \Drupal\file\Entity\File $file */
  $file = $reset ? \Drupal::service('entity_type.manager')->getStorage('file')->resetCache([$entity_id])->load($entity_id) : \Drupal::service('entity_type.manager')->getStorage('file')->load($entity_id);
}
