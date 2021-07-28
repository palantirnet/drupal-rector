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

    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // This needs to be replaced, but Rector was not yet able to replace this because the type of message was set with a variable. If you need to continue to use a variable, you might consider using a switch statement.
    // @noRector
    drupal_set_message($message, $type);
  }

}
