<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  \Drupal::database()->select('user');
}

/**
 * An example using alias.
 */
function alias() {
  \Drupal::database()->select('user', 'u');
}

/**
 * An example using alias and options.
 */
function alias_and_options() {
  \Drupal\core\Database\Database::getConnection('my_non_default_database')->select('user', 'u', []);
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

  \Drupal\core\Database\Database::getConnection('my_non_default_database')->select($table, $alias, $options);
}
