<?php

namespace Drupal\rector_examples;

use Drupal;
/**
 * Example of static method calls from a class.
 */
class DrupalRenderStaticUpdated {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    $elements = [
      '#markup' => '<div>hello world</div>',
    ];
    Drupal::service('renderer')->render($elements);
  }

  /**
   * An example using all parameters.
   */
  public function all_parameters() {
    $is_recursive_call = FALSE;
    $elements = [
      '#markup' => '<div>hello world</div>',
    ];
    Drupal::service('renderer')->render($elements, $is_recursive_call);
  }
}
