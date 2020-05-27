<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class DrupalURLStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    $url_as_string = \Drupal::url('user.login');
  }

  /**
   * An example using all parameters.
   */
  public function all_parameters() {
    $url_as_string = \Drupal::url('entity.node.canonical', ['node' => 1], ['query' => ['test_key' => 'test_value']], FALSE);

    $url_as_object = \Drupal::url('entity.node.canonical', ['node' => 1], ['query' => ['test_key' => 'test_value']], TRUE);
  }

}
