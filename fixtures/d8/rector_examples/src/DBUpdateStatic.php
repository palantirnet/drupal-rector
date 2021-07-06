<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class DBUpdateStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    $database = db_update('user');
  }

  /**
   * An example using options.
   */
  public function options() {
    $database = db_update('user', [
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

    $database = db_update($table, $options);
  }

}
