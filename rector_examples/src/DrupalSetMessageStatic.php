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
    drupal_set_message('example message');
  }

  /**
   * An example using all of the arguments.
   */
  public function using_all_arguments() {
    drupal_set_message('example warning', 'status', TRUE);
  }

  /**
   * Examples that show situations where we define the type of message.
   */
  public function message_types() {
    drupal_set_message('example error', 'error');

    drupal_set_message('example status', 'status');

    drupal_set_message('example warning', 'warning');
  }

  /**
   * This shows using a variable as the message type.
   *
   * This is rare, but used in Devel.
   */
  public function message_type_as_variable() {
    $message = 'example message from variable';

    $type = 'warning';

    drupal_set_message($message, $type);
  }

}
