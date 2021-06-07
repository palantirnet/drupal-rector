<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class DBQueryStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // You will need to use `\Drupal\core\Database\Database::getConnection()` if you do not yet have access to the container here.
    \Drupal::database()->query('select * from user');
  }

  /**
   * An example using placeholders as arguments.
   */
  public function placeholder() {
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // You will need to use `\Drupal\core\Database\Database::getConnection()` if you do not yet have access to the container here.
    \Drupal::database()->query('select * from user where name="%test"', ['%test'=>'Adam']);
  }

  /**
   * An example using arguments and options.
   */
  public function arguments_and_options() {
    \Drupal\core\Database\Database::getConnection('my_non_default_database')->query('select * from user where name="%test"', ['%test'=>'Adam'], [
      'fetch' => \PDO::FETCH_OBJ,
      'return' => Database::RETURN_STATEMENT,
      'throw_exception' => TRUE,
      'allow_delimiter_in_query' => FALSE,
    ]);
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

    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // If your `options` argument contains a `target` key, you will need to use `\Drupal\core\Database\Database::getConnection('my_database'). Drupal Rector could not yet evaluate the `options` argument since it was a variable.
    \Drupal::database()->query($query, $args, $opts);
  }

}
