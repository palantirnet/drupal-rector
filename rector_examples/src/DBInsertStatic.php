<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class DBInsertStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    db_insert('user');
  }

  /**
   * An example using options.
   */
  public function options() {
    db_insert('user', [
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

    db_insert($table, $options);
  }

}
