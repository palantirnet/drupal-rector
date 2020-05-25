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
    db_delete('path_alias');
  }

  /**
   * An example using options.
   */
  public function options() {
    db_delete('user', [
      'target' => 'my_non_default_database',
    ]);
  }

  /**
   * An example using variables for the table and options.
   */
  public function table_and_options_as_variables() {
    $table = 'user';

    $options = [
      'target' => 'my_non_default_database',
    ];

    db_delete($table, $options);
  }

  /**
   * An example using chained method calls.
   */
  public function chained_method_calls() {
    db_delete('path_alias')
      ->condition('path', '/my-path')
      ->execute();
  }

}
