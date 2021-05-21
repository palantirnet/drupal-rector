<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  $nodes = node_load_multiple([123, 456]);
}

/**
 * An example using all of the arguments.
 */
function all_arguments() {
  $nodes = node_load_multiple([123, 456], TRUE);
}

/**
 * An example using a variable for the argument.
 */
function all_arguments_as_variables() {
  $node_ids = [123, 456];
  $reset = TRUE;
  $nodes = node_load_multiple($node_ids, $reset);
}
