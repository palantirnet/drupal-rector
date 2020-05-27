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
    $url_as_string = Url::fromRoute('user.login')->toString();
  }

  /**
   * An example using all parameters.
   */
  public function all_parameters() {
    $url_as_string = Url::fromRoute('entity.node.canonical', ['node' => 1], ['query' => ['test_key' => 'test_value']])->toString(FALSE);

    $url_as_object = Url::fromRoute('entity.node.canonical', ['node' => 1], ['query' => ['test_key' => 'test_value']])->toString(TRUE);
  }

}
