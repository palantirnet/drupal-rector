<?php

use Drupal\Core\File\FileSystemInterface;
/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  $x = FileSystemInterface::EXISTS_REPLACE;
}

/**
 * An example using the constant as an argument.
 */
function as_an_argument() {
  file_unmanaged_copy('/test/directory', '/test/directory/new' . '/file_name.json', FileSystemInterface::EXISTS_REPLACE);
}
