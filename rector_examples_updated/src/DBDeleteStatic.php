<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class DBDeleteStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    \Drupal::database()->delete('path_alias');
  }

  /**
   * An example using options.
   */
  public function options() {
    \Drupal\core\Database\Database::getConnection('my_non_default_database')->delete('user', []);
  }

  /**
   * An example using variables for the table and options.
   */
  public function table_and_options_as_variables() {
    $table = 'user';

    $options = [
      'target' => 'my_non_default_database',
    ];

    \Drupal::database()->delete($table, $options);
  }

  /**
   * An example using chained method calls.
   */
  public function chained_method_calls() {
    \Drupal::database()->delete('path_alias')
      ->condition('path', '/my-path')
      ->execute();
  }

}
