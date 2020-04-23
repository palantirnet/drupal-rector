<?php

namespace Drupal\rector_examples;

use Drupal\Core\File\FileSystemInterface;
/**
 * Example of static method calls from a class.
 */
class FileUnmanagedSaveDataStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    \Drupal::service('file_system')->saveData('example');
  }

  /**
   * An example using all of the arguments.
   */
  public function using_all_arguments() {
    $snippet = 'example';
    $destination = "public://test/test.txt";

    \Drupal::service('file_system')->saveData($snippet, $destination, FileSystemInterface::EXISTS_REPLACE);
  }

  /**
   * This shows using a variable as the options.
   */
  public function options_as_variable() {
    $snippet = 'example';
    $destination = "public://test/test.txt";
    $options = FileSystemInterface::EXISTS_REPLACE;

    \Drupal::service('file_system')->saveData($snippet, $destination, $options);
  }

}
