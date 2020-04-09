<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class DBSelectStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    db_select('user');
  }

  /**
   * An example using alias.
   */
  public function alias() {
    db_select('user', 'u');
  }

  /**
   * An example using alias and options.
   */
  public function alias_and_options() {
    db_select('user', 'u', [
      'target' => 'my_non_default_database',
    ]);
  }

  /**
   * An example using variables for the table and alias and options.
   */
  public function table_and_alias_and_options_as_variables() {
    $table = 'user';

    $alias = 'u';

    $options = [
      'target' => 'my_non_default_database',
    ];

    db_select($table, $alias, $options);
  }

}
