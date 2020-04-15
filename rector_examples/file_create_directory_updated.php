<?php

use \Drupal\Core\File\FileSystemInterface;

/**
 * This demonstrates the updated deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  $x = \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY;
}

/**
 * An example using the constant as an argument.
 */
function as_an_argument() {
  \Drupal::service('file_system')->prepareDirectory('/test/directory', FileSystemInterface::CREATE_DIRECTORY);
}
