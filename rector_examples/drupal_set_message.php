<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  drupal_set_message('example message');
}

/**
 * An example using all of the arguments.
 */
function using_all_arguments() {
  drupal_set_message('example warning', 'status', TRUE);
}

/**
 * Examples that show situations where we define the type of message.
 */
function message_types() {
  drupal_set_message('example error', 'error');

  drupal_set_message('example status', 'status');

  drupal_set_message('example warning', 'warning');
}

/**
 * This shows using a variable as the message type.
 *
 * This is rare, but used in Devel.
 */
function message_type_as_variable() {
  $message = 'example message from variable';

  $type = 'warning';

  drupal_set_message($message, $type);
}
