<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  \Drupal::database()->insert('user');
}

/**
 * An example using options.
 */
function options() {
  \Drupal\core\Database\Database::getConnection('my_non_default_database')->insert('user', []);
}

/**
 * An example using variables for the table and options.
 */
function table_and_options_as_variables() {
  $table = 'user';

  $options = [
    'target' => 'my_non_default_database',
  ];

  \Drupal::database()->insert($table, $options);
}
