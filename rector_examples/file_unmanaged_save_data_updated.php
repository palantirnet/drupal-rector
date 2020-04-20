<?php

use Drupal\Core\File\FileSystemInterface;
/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  \Drupal::service('file_system')->saveData('example');
}

/**
 * An example using all of the arguments.
 */
function using_all_arguments() {
  $snippet = 'example';
  $destination = "public://test/test.txt";

  \Drupal::service('file_system')->saveData($snippet, $destination, FileSystemInterface::EXISTS_REPLACE);
}

/**
 * This shows using a variable as the options.
 */
function options_as_variable() {
  $snippet = 'example';
  $destination = "public://test/test.txt";
  $options = FileSystemInterface::EXISTS_REPLACE;

  \Drupal::service('file_system')->saveData($snippet, $destination, $options);
}
