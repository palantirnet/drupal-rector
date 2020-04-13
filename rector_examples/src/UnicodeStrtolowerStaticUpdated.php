<?php

namespace Drupal\rector_examples;

/**
 * Example of updated static method calls from a class.
 */
class UnicodeStrtolowerStaticUpdated {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    $string = mb_strtolower('example');
  }

  /**
   * Example of using a use statment instead of a fully qualified class name.
   */
  public function example_with_use_statement() {
    $string = mb_strtolower('example');
  }

}
