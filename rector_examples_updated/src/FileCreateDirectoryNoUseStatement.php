<?php

namespace Drupal\rector_examples;

use Drupal\Core\File\FileSystemInterface;
/**
 * Example of method calls from a class without use statements.
 */
class FileCreateDirectoryNoUseStatement {

  /**
   * A simple example.
   */
  public function simple_example() {
    $x = FileSystemInterface::CREATE_DIRECTORY;
  }

  /**
   * An example using the constant as an argument.
   */
  public function as_an_argument() {
    \Drupal::service('file_system')->prepareDirectory('/test/directory', FileSystemInterface::CREATE_DIRECTORY);
  }

}
