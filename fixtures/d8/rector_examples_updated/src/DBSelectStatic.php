<?php

namespace Drupal\rector_examples;

use Drupal\core\Database\Database;
/**
 * Example of static method calls from a class.
 */
class DBSelectStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // You will need to use `\Drupal\core\Database\Database::getConnection()` if you do not yet have access to the container here.
    \Drupal::database()->select('user');
  }

  /**
   * An example using alias.
   */
  public function alias() {
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // You will need to use `\Drupal\core\Database\Database::getConnection()` if you do not yet have access to the container here.
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

    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // If your `options` argument contains a `target` key, you will need to use `\Drupal\core\Database\Database::getConnection('my_database'). Drupal Rector could not yet evaluate the `options` argument since it was a variable.
    \Drupal::database()->select($table, $alias, $options);
  }

}
