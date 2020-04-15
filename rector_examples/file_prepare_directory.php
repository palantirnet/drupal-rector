<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  $directory = '/test/directory';

  file_prepare_directory($directory);
}

/**
 * An example using all of the arguments.
 */
function using_all_arguments() {
  $directory = '/test/directory';

  file_prepare_directory($directory, FILE_CREATE_DIRECTORY);
}

/**
 * This shows using a variable as the options.
 */
function options_as_variable() {
  $directory = '/test/directory';

  $options = FILE_CREATE_DIRECTORY;

  file_prepare_directory($directory, $options);
}
