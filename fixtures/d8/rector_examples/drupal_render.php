<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  $elements = [
    '#markup' => '<div>hello world</div>',
  ];
  drupal_render($elements);
}

/**
 * An example using all of the arguments.
 */
function using_all_arguments() {
  $is_recursive_call = FALSE;
  $elements = [
    '#markup' => '<div>hello world</div>',
  ];
  drupal_render($elements, $is_recursive_call);
}
