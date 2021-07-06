<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  $safe_string_markup_object = \Drupal\Component\Utility\SafeMarkup::format('hello world');
}

/**
 * An example using all arguments.
 */
function all_arguments() {
  $safe_string_markup_object = \Drupal\Component\Utility\SafeMarkup::format('hello @placeholder', ['@my_placeholder' => 'world']);
}
