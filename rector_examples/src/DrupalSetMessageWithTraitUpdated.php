<?php

namespace Drupal\rector_examples;

use Drupal\Core\Messenger\MessengerTrait;

class DrupalSetMessageWithTraitUpdated {

  use MessengerTrait;

  /**
   * Example of updated static calls from a class with the trait.
   *
   * @return array
   */
  public function example() {
    $this->messenger()->addStatus('example message');

    $this->messenger()->addError('example error');

    $this->messenger()->addStatus('example status');

    $this->messenger()->addWarning('example warning');

    $this->messenger()->addStatus('example warning', TRUE);

    return NULL;
  }

}
