<?php

use Drupal\Component\Utility\Unicode;

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  $string = mb_strtolower('example');
}

/**
 * Example of using a use statment instead of a fully qualified class name.
 */
function example_with_use_statement() {
  $string = mb_strtolower('example');
}
