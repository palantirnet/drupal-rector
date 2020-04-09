<?php

namespace Drupal\rector_examples;

use Drupal\Core\Database\Database;

/**
 * Example of static method calls from a class.
 */
class DBSelectStaticUpdated {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    \Drupal::database()->select('user');
  }

  /**
   * An example using alias.
   */
  public function alias() {
    \Drupal::database()->select('user', 'u');
  }

  /**
   * An example using alias and options.
   */
  public function alias_and_options() {
    Database::getConnection('my_non_default_database')->select('user', 'u', []);
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

    Database::getConnection('my_non_default_database')->select($table, $alias, $options);
  }

}
