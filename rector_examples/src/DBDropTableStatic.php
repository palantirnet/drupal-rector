<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class DBDropTableStatic {

  /**
   * An example using the variable table.
   */
  public function simple_example() {
    db_drop_table('path_alias');
  }
}
