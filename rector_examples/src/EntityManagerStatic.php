<?php

namespace Drupal\rector_examples;

/**
 * Example of static method calls from a class.
 */
class EntityManagerStatic {

  /**
   * Simple example
   */
  public function simple_example() {
    $entity_manager = \Drupal::entityManager();
  }

  /**
   * Example of using a method directly on the service.
   */
  public function method_on_service() {
    $definitions = \Drupal::entityManager()->getDefinitions();
  }

  /**
   * Example of using a method that is not in entityTypeManager.
   *
   * These should now use the `entity_type.repository` service.
   */
  public function method_not_in_entityTypeManager() {
    $group = FALSE;

    $entity_manager = \Drupal::entityManager()->getEntityTypeLabels($group);
  }

  /**
   * Example of storing the services and then calling methods not in entityTypeManager.
   *
   * These should now use the `entity_type.repository` service.
   */
  public function stored_service_and_method_not_in_entityTypeManager() {
    $entity_manager = \Drupal::entityManager();

    $group = FALSE;
    $class_name = 'MyClass';

    $entity_manager->getEntityTypeLabels($group);
    $entity_manager->getEntityTypeFromClass($class_name);
  }

}
