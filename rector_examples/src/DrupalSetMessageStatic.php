<?php

/**
 * @file
 * Contains \Drupal\rector_examples\DrupalSetMessageStatic.
 */

namespace Drupal\rector_examples;

class DrupalSetMessageStatic {

  /**
   * Example of static method calls from a class.
   *
   * @return null
   */
  public function example() {
    drupal_set_message('example message');

    drupal_set_message('example error', 'error');

    drupal_set_message('example status', 'status');

    drupal_set_message('example warning', 'warning');

    drupal_set_message('example warning', 'status', TRUE);

    return NULL;
  }

}
