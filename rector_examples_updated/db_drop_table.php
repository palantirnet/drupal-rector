<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // You will need to use `\Drupal\core\Database\Database::getConnection()` if you do not yet have access to the container here.
  \Drupal::database()->schema()->dropTable('user');
}
