<?php

/**
 * This demonstrates the updated deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  \Drupal::database()->query('select * from user');
}

/**
 * An example using placeholders as arguments.
 */
function placeholder() {
  \Drupal::database()->query('select * from user where name="%test"', ['%test'=>'Adam']);
}

/**
 * An example using arguments and options.
 */
function arguments_and_options() {
  \Drupal\core\Database\Database::getConnection('my_non_default_database')->query('select * from user where name="%test"', ['%test'=>'Adam'], ['fetch' => \PDO::FETCH_OBJ, 'return' => Database::RETURN_STATEMENT, 'throw_exception' => TRUE, 'allow_delimiter_in_query' => FALSE]);
}

/**
 * An example using variables for the query, args, and options.
 */
function query_and_arguments_and_options_as_variables() {
  $query = 'select * from user where name="%test"';

  $args = ['%test' => 'Adam'];

  $opts = ['target' => 'my_non_default_database',
    'fetch' => \PDO::FETCH_OBJ,
    'return' => Database::RETURN_STATEMENT,
    'throw_exception' => TRUE,
    'allow_delimiter_in_query' => FALSE,
  ];

  \Drupal::database()->query($query, $args, $opts);
}
