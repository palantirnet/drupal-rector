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

    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // This needs to be replaced, but Rector was not yet able to replace this because the type of message was set with a variable. If you need to continue to use a variable, you might consider using a switch statement.
    // @noRector
    drupal_set_message($message, $type);
  }

}
