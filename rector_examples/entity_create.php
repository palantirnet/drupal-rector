<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  $entity = entity_create('node');
}

/**
 * An example using all of the arguments.
 */
function all_arguments() {
  $entity = entity_create('node', ['bundle' => 'page', 'title' => 'Hello world']);
}
