<?php

namespace Drupal\rector_examples\Plugin\Controller;

use Drupal\Core\Controller\ControllerBase;

class EntityManagerController extends ControllerBase {

  /**
   * Example of method calls from a class.
   *
   * @return null
   */
  public function example() {
    // Setting the service to the local variable.
    $entity_manager = $this->entityManager();

    // Using a method on the service directly.
    $definitions = $this->entityManager()->getDefinitions();

    // Using methods that are not in the entityTypeManager service.
    $entity_manager = $this->entityManager();

    $group = FALSE;
    $class_name = 'MyClass';

    $entity_manager->getEntityTypeLabels($group);
    $entity_manager->getEntityTypeFromClass($class_name);

    $entity_manager = $this->entityManager()->getEntityTypeLabels($group);

    return NULL;
  }

}
