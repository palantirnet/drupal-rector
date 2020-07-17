<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  file_unmanaged_save_data('example');
}

/**
 * An example using all of the arguments.
 */
function using_all_arguments() {
  $snippet = 'example';
  $destination = "public://test/test.txt";

  file_unmanaged_save_data($snippet, $destination, FILE_EXISTS_REPLACE);
}

/**
 * This shows using a variable as the options.
 */
function options_as_variable() {
  $snippet = 'example';
  $destination = "public://test/test.txt";
  $options = FILE_EXISTS_REPLACE;

  file_unmanaged_save_data($snippet, $destination, $options);
}
