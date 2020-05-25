<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  $database = \Drupal::database()->update('user');
}

/**
 * An example using options.
 */
function options() {
  $database = \Drupal\core\Database\Database::getConnection('my_non_default_database')->update('user', []);
}

/**
 * An example using variables for the table and options.
 */
function table_and_options_as_variables() {
  $table = 'user';

  $options = [
    'target' => 'my_non_default_database',
  ];

  $database = \Drupal::database()->update($table, $options);
}
