<?php

use Drupal\Core\Database\Connection;
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
  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // This is assuming we want to use `$escapedTables`, but you may need to use `$escapedFields` instead.
  $escaped_names = Connection::$escapedTables;
}
