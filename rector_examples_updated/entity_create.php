<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  $entity = \Drupal::service('entity_type.manager')->getStorage('node')->create([]);
}

/**
 * An example using all of the arguments.
 */
function all_arguments() {
  $entity = \Drupal::service('entity_type.manager')->getStorage('node')->create(['bundle' => 'page', 'title' => 'Hello world']);
}
