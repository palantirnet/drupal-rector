<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

function example() {
  drupal_set_message('example message');

  drupal_set_message('example error', 'error');

  drupal_set_message('example status', 'status');

  drupal_set_message('example warning', 'warning');

  drupal_set_message('example warning', 'status', TRUE);

  $message = 'example message from variable';

  $type = 'warning';

  drupal_set_message($message, $type);
}

function updated() {
  \Drupal::messenger()->addStatus('example message');

  \Drupal::messenger()->addError('example error');

  \Drupal::messenger()->addStatus('example status');

  \Drupal::messenger()->addWarning('example warning');

  \Drupal::messenger()->addStatus('example warning', TRUE);
}
