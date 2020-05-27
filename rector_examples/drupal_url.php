<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  $url_as_string = \Drupal::url('user.login');
}

/**
 * An example using all parameters.
 */
function all_parameters() {
  $url_as_string = \Drupal::url('entity.node.canonical', ['node' => 1], ['query' => ['test_key' => 'test_value']], FALSE);

  $url_as_object = \Drupal::url('entity.node.canonical', ['node' => 1], ['query' => ['test_key' => 'test_value']], TRUE);
}
