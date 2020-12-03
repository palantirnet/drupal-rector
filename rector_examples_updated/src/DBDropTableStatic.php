<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class DBDropTableStatic {

 /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // You will need to use `\Drupal\core\Database\Database::getConnection()` if you do not yet have access to the container here.
    \Drupal::database()->schema()->dropTable('path_alias');
  }

}
