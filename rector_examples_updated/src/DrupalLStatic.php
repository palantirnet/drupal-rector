<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class DrupalLStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    \Drupal::service('link_generator')->generate('User Login', \Drupal::service('url_generator')->generateFromRoute('user.login'));
  }

}
