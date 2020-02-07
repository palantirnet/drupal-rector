<?php

namespace Drupal\rector_examples\Plugin\Controller;

use Drupal\Core\Controller\ControllerBase;

class EntityManagerController extends ControllerBase {

  /**
   * Example of static method calls from a class.
   *
   * @return null
   */
  public function example() {
    $entity_manager = $this->entityManager();

    return NULL;
  }

}
