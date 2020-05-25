<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  db_delete('user');
}

/**
 * An example using options.
 */
function options() {
  db_delete('user', [
    'target' => 'my_non_default_database',
  ]);
}

/**
 * An example using variables for the table and options.
 */
function table_and_options_as_variables() {
  $table = 'user';

  $options = [
    'target' => 'my_non_default_database',
  ];

  db_delete($table, $options);
}