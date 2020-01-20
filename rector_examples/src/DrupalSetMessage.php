<?php

/**
 * @file
 * Contains \Drupal\rector_examples\DrupalSetMessage.
 */

namespace Drupal\rector_examples;

class DrupalSetMessage {

  /**
   * Return an example data structure.
   *
   * @return array
   */
  public function example() {
    drupal_set_message('example message');

    drupal_set_message('example error', 'error');

    drupal_set_message('example status', 'status');

    drupal_set_message('example warning', 'warning');

    return NULL;
  }

}
