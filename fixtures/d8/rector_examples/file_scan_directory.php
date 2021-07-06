<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  $directory = '/test/directory';

  file_scan_directory($directory);
}

/**
 * An example using all of the arguments.
 */
function using_all_arguments() {
  $directory = '/test/directory';
  $mask = '/^' . DRUPAL_PHP_FUNCTION_PATTERN . '$/';
  $options = [
    'callback' => 0,
    'recurse' => TRUE,
    'key' => 'uri',
    'min_depth' => 0,
  ];
  file_scan_directory($directory, $mask, $options);
}
