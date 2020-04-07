<?php

namespace Drupal\rector_examples;

use Drupal\Core\Database\Database;

/**
 * Example of updated static method calls from a class.
 */
class DBQueryUpdated {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    \Drupal::database()->query('select * from user');
  }

  /**
   * An example using placeholders as arguments.
   */
  public function placeholder() {
    \Drupal::database()->query('select * from user where name="%test"', ['%test'=>'Adam']);
  }

  /**
   * An example using arguments and options.
   */
  public function arguments_and_options() {
    Database::getConnection('my_non_default_database')->query('select * from user where name="%test"', ['%test'=>'Adam'], ['fetch' => \PDO::FETCH_OBJ, 'return' => Database::RETURN_STATEMENT, 'throw_exception' => TRUE, 'allow_delimiter_in_query' => FALSE]);
  }

  /**
   * An example using variables for the query, args, and options.
   */
  public function query_and_arguments_and_options_as_variables() {
    $query = 'select * from user where name="%test"';

    $args = ['%test' => 'Adam'];

    $opts = ['target' => 'my_non_default_database',
      'fetch' => \PDO::FETCH_OBJ,
      'return' => Database::RETURN_STATEMENT,
      'throw_exception' => TRUE,
      'allow_delimiter_in_query' => FALSE,
    ];

    Database::getConnection('my_non_default_database')->query($query, $args, $opts);
  }

}
