<?php

namespace Drupal\rector_examples;

use Drupal\Core\Url;
/**
 * Example of static method calls from a class.
 */
class DrupalURLStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    Url::fromRoute('user.login')->toString();
  }

  /**
   * An example using all parameters.
   */
  public function all_parameters() {
    Url::fromRoute('entity.node.canonical', ['node' => 1], ['query' => ['test_key' => 'test_value']])->toString(FALSE);
  }

}
