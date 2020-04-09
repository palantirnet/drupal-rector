<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  db_query('select * from user');
}

/**
 * An example using placeholders as arguments.
 */
function placeholder() {
  db_query('select * from user where name="%test"', ['%test'=>'Adam']);
}

/**
 * An example using arguments and options.
 */
function arguments_and_options() {
  db_query('select * from user where name="%test"', ['%test'=>'Adam'], [
    'target' => 'my_non_default_database',
    'fetch' => \PDO::FETCH_OBJ,
    'return' => Database::RETURN_STATEMENT,
    'throw_exception' => TRUE,
    'allow_delimiter_in_query' => FALSE,
  ]);
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

  db_query($query, $args, $opts);
}
