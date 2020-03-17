<?php

namespace Drupal\rector_examples;

use Drupal\Core\Controller\ControllerBase;

class EntityManagerControllerBase extends ControllerBase {

  /**
   * Example of a ...
   *
   * @return null
   */
  public function example() {
    $entity_manager = $this->entityManager();
    $node_storage = $entity_manager->getStorage('node');

    $node_storage = $this->entityManager()->getStorage('node');

    $node_storage = $this->entityTypeManager()->getStorage('node');

    // This should use a different service.
    $entity_manager = $this->entityManager();
    $groups = $entity_manager->getEntityTypeLabels('example');

    $groups = $this->entityManager()->getEntityTypeLabels('example');

    $this->entityTypeRepository->getEntityTypeLabels('example');

    $groups = \Drupal::service('entity_type.repository')->getEntityTypeLabels('example');

    return NULL;
  }

}
