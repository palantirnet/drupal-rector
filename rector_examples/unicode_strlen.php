<?php

use Drupal\Component\Utility\Unicode;

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  $length = \Drupal\Component\Utility\Unicode::strlen('example');
}
