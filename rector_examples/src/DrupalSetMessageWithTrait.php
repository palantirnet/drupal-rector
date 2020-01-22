<?php

/**
 * @file
 * Contains \Drupal\rector_examples\DrupalSetMessageWithTrait.
 */

namespace Drupal\rector_examples;

use Drupal\Core\Messenger\MessengerTrait;

class DrupalSetMessageWithTrait {

  use MessengerTrait;

  /**
   * Example of static calls from a class with the trait.
   *
   * @return array
   */
  public function example() {
    drupal_set_message('example message');

    drupal_set_message('example error', 'error');

    drupal_set_message('example status', 'status');

    drupal_set_message('example warning', 'warning');

    drupal_set_message('example warning', 'status', TRUE);

    $message = 'example message from variable';

    $type = 'warning';

    drupal_set_message($message, $type);

    return NULL;
  }

}
