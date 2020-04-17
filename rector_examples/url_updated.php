<?php

/**
 * This demonstrates the updated deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  \Drupal::service('url_generator')->generateFromRoute('user.login');
}

/**
 * An example using all parameters.
 */
function all_parameters() {
  \Drupal::service('url_generator')->generateFromRoute('entity.node.canonical', ['node' => 1], ['query' => ['test_key' => 'test_value']], FALSE);
}
