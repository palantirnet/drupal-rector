<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  $directory = '/test/directory';

  \Drupal::service('file_system')->prepareDirectory($directory);
}

/**
 * An example using all of the arguments.
 */
function using_all_arguments() {
  $directory = '/test/directory';

  \Drupal::service('file_system')->prepareDirectory($directory, 'FILE_CREATE_DIRECTORY');
}

/**
 * This shows using a variable as the options.
 */
function options_as_variable() {
  $directory = '/test/directory';

  $options = 'FILE_CREATE_DIRECTORY';

  \Drupal::service('file_system')->prepareDirectory($directory, $options);
}
