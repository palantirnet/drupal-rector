<?php

namespace Drupal\rector_examples;

use Drupal\Core\Database\Database;

/**
 * Example of static method calls from a class.
 */
class DBInsertStaticUpdated {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    \Drupal::database()->insert('path_alias');
  }

  /**
   * An example using options.
   */
  public function options() {
    Database::getConnection('my_non_default_database')->insert('user', []);
  }

  /**
   * An example using variables for the table and options.
   */
  public function table_and_options_as_variables() {
    $table = 'user';

    $options = [
      'target' => 'my_non_default_database',
    ];

    Database::getConnection('my_non_default_database')->insert($table, $options);
  }

  /**
   * An example using chained method calls.
   */
  public function chained_method_calls() {
    \Drupal::database()->insert('path_alias')
      ->fields(['path', 'alias'], [['/my-path', '/my-alias']])
      ->execute();
  }

}
