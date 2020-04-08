<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  db_select('user');
}

/**
 * An example using alias.
 */
function alias() {
  db_select('user', 'u');
}

/**
 * An example using alias and options.
 */
function alias_and_options() {
  db_select('user', 'u', [
    'target' => 'my_non_default_database',
  ]);
}

/**
 * An example using variables for the table and alias and options.
 */
function table_and_alias_and_options_as_variables() {
  $table = 'user';

  $alias = 'u';

  $options = [
    'target' => 'my_non_default_database',
  ];

  db_select($table, $alias, $options);
}
