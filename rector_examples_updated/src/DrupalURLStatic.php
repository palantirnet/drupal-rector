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
    \Drupal::service('url_generator')->generateFromRoute('user.login');
  }

  /**
   * An example using all parameters.
   */
  public function all_parameters() {
    \Drupal::service('url_generator')->generateFromRoute('entity.node.canonical', ['node' => 1], ['query' => ['test_key' => 'test_value']], FALSE);
  }

}
