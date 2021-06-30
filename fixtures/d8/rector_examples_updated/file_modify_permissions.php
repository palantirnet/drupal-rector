<?php

use Drupal\Core\File\FileSystemInterface;
/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  $x = FileSystemInterface::MODIFY_PERMISSIONS;
}

/**
 * An example using the constant as an argument.
 */
function as_an_argument() {
  \Drupal::service('file_system')->prepareDirectory('/test/directory', FileSystemInterface::MODIFY_PERMISSIONS);
}
