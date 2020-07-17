<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 *
 * This isn't a fantastic example, but it was built to test Drupal 9 integration.
 * It might make sense to remove it later.
 */

/**
 * A simple example.
 */
function simple_example() {
  $escaped_names = \Drupal\Core\Database\Connection::$escapedNames;
}
