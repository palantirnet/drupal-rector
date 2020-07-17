<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  $file_default_scheme = \Drupal::config('system.file')->get('default_scheme');
}
