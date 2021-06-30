<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class FilePrepareDirectoryStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    $directory = '/test/directory';

    file_prepare_directory($directory);
  }

  /**
   * An example using all of the arguments.
   */
  public function using_all_arguments() {
    $directory = '/test/directory';

    file_prepare_directory($directory, FILE_CREATE_DIRECTORY);
  }

  /**
   * This shows using a variable as the options.
   */
  public function options_as_variable() {
    $directory = '/test/directory';

    $options = FILE_CREATE_DIRECTORY;

    file_prepare_directory($directory, $options);
  }

}
