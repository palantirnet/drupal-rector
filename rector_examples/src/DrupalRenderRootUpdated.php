<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class DrupalRenderRootStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
      \Drupal::service('renderer')->renderRoot($elements);
  }

  /**
   * An example using all parameters.
   */
  public function all_parameters() {
    $is_recursive_call = false;
    \Drupal::service('renderer')->renderRoot($elements, $is_recursive_call);
  }
}
