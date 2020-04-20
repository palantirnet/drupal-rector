<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class FileUnmanagedSaveDataStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    file_unmanaged_save_data('example');
  }

  /**
   * An example using all of the arguments.
   */
  public function using_all_arguments() {
    $snippet = 'example';
    $destination = "public://test/test.txt";

    file_unmanaged_save_data($snippet, $destination, FILE_EXISTS_REPLACE);
  }

  /**
   * This shows using a variable as the options.
   */
  public function options_as_variable() {
    $snippet = 'example';
    $destination = "public://test/test.txt";
    $options = FILE_EXISTS_REPLACE;

    file_unmanaged_save_data($snippet, $destination, $options);
  }

}
