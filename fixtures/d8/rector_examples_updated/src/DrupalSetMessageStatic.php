<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class DrupalSetMessageStatic {

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    \Drupal::messenger()->addStatus('example message');
  }

  /**
   * An example using all of the arguments.
   */
  public function using_all_arguments() {
    \Drupal::messenger()->addStatus('example warning', TRUE);
  }

  /**
   * Examples that show situations where we define the type of message.
   */
  public function message_types() {
    \Drupal::messenger()->addError('example error');

    \Drupal::messenger()->addStatus('example status');

    \Drupal::messenger()->addWarning('example warning');
  }

  /**
   * This shows using a variable as the message type.
   *
   * This is rare, but used in Devel.
   */
  public function message_type_as_variable() {
    $message = 'example message from variable';

    $type = 'warning';

    \Drupal::messenger()->addWarning($message);
  }

}
