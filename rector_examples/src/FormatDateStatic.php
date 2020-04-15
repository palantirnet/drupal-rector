<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class FormatDateStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    $formatted_date = format_date(123456);
  }

  /**
   * An example using all of the arguments.
   */
  public function using_all_arguments() {
    $formatted_date = format_date(time(), 'custom', 'Y M D', 'America/Los_Angeles', 'en-us');
  }

}
