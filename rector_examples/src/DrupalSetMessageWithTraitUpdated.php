<?php

namespace Drupal\rector_examples;

use Drupal\Core\Messenger\MessengerTrait;

/**
 * Example of updated static calls from a class with the trait.
 */
class DrupalSetMessageWithTraitUpdated {

  use MessengerTrait;

  /**
   * A simple example using the minimum number of arguments.
   */
  public function simple_example() {
    $this->messenger()->addStatus('example message');
  }

  /**
   * An example using all of the arguments.
   */
  public function using_all_arguments() {
    $this->messenger()->addStatus('example warning', TRUE);
  }

  /**
   * Examples that show situations where we define the type of message.
   */
  public function message_types() {
    $this->messenger()->addError('example error');

    $this->messenger()->addStatus('example status');

    $this->messenger()->addWarning('example warning');
  }

  /**
   * This shows using a variable as the message type.
   *
   * This is rare, but used in Devel.
   */
  public function message_type_as_variable() {
    $message = 'example message from variable';

    $type = 'warning';

    switch($type) {
      case 'warning':
        $this->messenger()->addWarning($message);
        break;
      case 'error':
        $this->messenger()->addError($message);
        break;
      default:
        $this->messenger()->addStatus($message);
    }
  }

}
