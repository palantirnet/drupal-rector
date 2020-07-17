<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  /* @var \Drupal\user\Entity\User $user */
  $user = user_load(123);
}

/**
 * An example using all of the arguments.
 */
function all_arguments() {
  /* @var \Drupal\user\Entity\User $user */
  $user = user_load(123, TRUE);
}

/**
 * An example using all of the arguments as variables.
 */
function all_arguments_as_variables() {
  $entity_id = 123;
  $reset = TRUE;

  /* @var \Drupal\user\Entity\User $user */
  $user = user_load($entity_id, $reset);
}
