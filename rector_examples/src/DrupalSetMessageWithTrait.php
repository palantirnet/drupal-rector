<?php

namespace Drupal\rector_examples;

use Drupal\Core\Messenger\MessengerTrait;

/**
 * Example of static calls from a class with the trait.
 *
 * This is basically copied from `DrupalSetMessageStatic.php`.
 */
class DrupalSetMessageWithTrait {

  use MessengerTrait;

  /**
   * A simple example using the minimum number of arguments.
   *
   * @return null
   */
  public function simple_example() {
    drupal_set_message('example message');

    return NULL;
  }

  /**
   * An example using all of the arguments.
   *
   * @return null
   */
  public function using_all_arguments() {
    drupal_set_message('example warning', 'status', TRUE);

    return NULL;
  }

  /**
   * Examples that show situations where we define the type of message.
   *
   * @return null
   */
  public function message_types() {
    drupal_set_message('example error', 'error');

    drupal_set_message('example status', 'status');

    drupal_set_message('example warning', 'warning');

    return NULL;
  }

  /**
   * This shows using a variable as the message type.
   *
   * This is rare, but used in Devel.
   *
   * @return null
   */
  public function message_type_as_variable() {
    $message = 'example message from variable';

    $type = 'warning';

    drupal_set_message($message, $type);

    return NULL;
  }

}
