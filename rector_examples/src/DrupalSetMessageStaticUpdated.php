<?php

namespace Drupal\rector_examples;

class DrupalSetMessageStaticUpdated {

  /**
   * Example of updated static method calls from a class.
   *
   * @return null
   */
  public function example() {
    \Drupal::messenger()->addStatus('example message');

    \Drupal::messenger()->addError('example error');

    \Drupal::messenger()->addStatus('example status');

    \Drupal::messenger()->addWarning('example warning');

    \Drupal::messenger()->addStatus('example warning', TRUE);

    return NULL;
  }

}
