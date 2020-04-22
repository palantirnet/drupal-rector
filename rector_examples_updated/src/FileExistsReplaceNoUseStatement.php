<?php

namespace Drupal\rector_examples;

use Drupal\Core\File\FileSystemInterface;
/**
 * Example of method calls from a class without use statements.
 */
class FileExistsReplaceNoUseStatement {

  /**
   * A simple example.
   */
  public function simple_example() {
    $x = FileSystemInterface::EXISTS_REPLACE;
  }

  /**
   * An example using the constant as an argument.
   */
  public function as_an_argument() {
    file_unmanaged_copy('/test/directory', '/test/directory/new' . '/file_name.json', FileSystemInterface::EXISTS_REPLACE);
  }

}
