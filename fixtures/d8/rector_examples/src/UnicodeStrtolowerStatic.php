<?php

namespace Drupal\rector_examples;

use \Drupal\Component\Utility\Unicode;

/**
 * Example of static method calls from a class.
 */
class UnicodeStrtolowerStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    $string = \Drupal\Component\Utility\Unicode::strtolower('example');
  }

  /**
   * Example of using a use statment instead of a fully qualified class name.
   */
  public function example_with_use_statement() {
    $string = Unicode::strtolower('example');
  }

}
