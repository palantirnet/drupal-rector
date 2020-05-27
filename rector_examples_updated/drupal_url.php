<?php

use Drupal\Core\Url;
/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  Url::fromRoute('user.login')->toString();
}

/**
 * An example using all parameters.
 */
function all_parameters() {
  Url::fromRoute('entity.node.canonical', ['node' => 1], ['query' => ['test_key' => 'test_value']])->toString(FALSE);
}
