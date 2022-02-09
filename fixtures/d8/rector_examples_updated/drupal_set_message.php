<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example using the minimum number of arguments.
 */
function simple_example() {
  \Drupal::messenger()->addStatus('example message');
}

/**
 * An example using all of the arguments.
 */
function using_all_arguments() {
  \Drupal::messenger()->addStatus('example warning', TRUE);
}

/**
 * Examples that show situations where we define the type of message.
 */
function message_types() {
  \Drupal::messenger()->addError('example error');

  \Drupal::messenger()->addStatus('example status');

  \Drupal::messenger()->addWarning('example warning');
}

/**
 * This shows using a variable as the message type.
 *
 * This is rare, but used in Devel.
 */
function message_type_as_variable() {
  $message = 'example message from variable';

  $type = 'warning';

  \Drupal::messenger()->addWarning($message);
}
