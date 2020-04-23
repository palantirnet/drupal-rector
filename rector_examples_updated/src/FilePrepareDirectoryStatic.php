<?php

namespace Drupal\rector_examples;

use Drupal\Core\File\FileSystemInterface;
/**
 * Example of static method calls from a class.
 */
class FilePrepareDirectoryStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    $directory = '/test/directory';

    \Drupal::service('file_system')->prepareDirectory($directory);
  }

  /**
   * An example using all of the arguments.
   */
  public function using_all_arguments() {
    $directory = '/test/directory';

    \Drupal::service('file_system')->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);
  }

  /**
   * This shows using a variable as the options.
   */
  public function options_as_variable() {
    $directory = '/test/directory';

    $options = FileSystemInterface::CREATE_DIRECTORY;

    \Drupal::service('file_system')->prepareDirectory($directory, $options);
  }

}
