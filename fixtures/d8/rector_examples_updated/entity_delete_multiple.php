<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  \Drupal::service('entity_type.manager')->getStorage('node')->delete(\Drupal::service('entity_type.manager')->getStorage('node')->loadMultiple([1, 2, 3]));
}
