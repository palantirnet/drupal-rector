<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  \Drupal::url('user.login');
}

/**
 * An example using all parameters.
 */
function all_parameters() {
  \Drupal::url('entity.node.canonical', ['node' => 1], ['query' => ['test_key' => 'test_value']], FALSE);
}
