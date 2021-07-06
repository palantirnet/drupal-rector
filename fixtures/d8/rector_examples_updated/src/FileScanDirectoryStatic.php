<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class FileScanDirectoryStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    $directory = '/test/directory';

    \Drupal::service('file_system')->scanDirectory($directory);
  }

  /**
   * An example using all of the arguments.
   */
  public function using_all_arguments() {
    $directory = '/test/directory';
    $mask = '/^' . DRUPAL_PHP_FUNCTION_PATTERN . '$/';
    $options = [
      'callback' => 0,
      'recurse' => TRUE,
      'key' => 'uri',
      'min_depth' => 0,
    ];
    \Drupal::service('file_system')->scanDirectory($directory, $mask, $options);
  }

}
