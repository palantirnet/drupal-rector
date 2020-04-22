<?php

namespace Drupal\rector_examples\Plugin\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Examples of `$this->entityManager()` calls from a class that extends `ControllerBase`.
 */
class EntityManagerController extends ControllerBase {

  /**
   * Simple example
   *
   * @return null
   */
  public function simple_example() {
    $entity_manager = $this->entityManager();

    return NULL;
  }

  /**
   * Example of using a method directly on the service.
   *
   * @return null
   */
  public function method_on_service() {
    $definitions = $this->entityManager()->getDefinitions();

    return NULL;
  }

  /**
   * Example of using a method that is not in entityTypeManager.
   *
   * These should now use the `entity_type.repository` service.
   *
   * @return null
   */
  public function method_not_in_entityTypeManager() {
    $group = FALSE;

    $entity_manager = $this->entityManager()->getEntityTypeLabels($group);

    return NULL;
  }

  /**
   * Example of storing the services and then calling methods not in entityTypeManager.
   *
   * These should now use the `entity_type.repository` service.
   *
   * @return null
   */
  public function stored_service_and_method_not_in_entityTypeManager() {
    $entity_manager = $this->entityManager();

    $group = FALSE;
    $class_name = 'MyClass';

    $entity_manager->getEntityTypeLabels($group);
    $entity_manager->getEntityTypeFromClass($class_name);

    return NULL;
  }

}
