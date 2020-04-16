<?php

namespace Drupal\rector_examples;

use Drupal;
/**
 * Example of updated static method calls from a class.
 */
class EntityManagerStaticUpdated {

  /**
   * Simple example
   */
  public function simple_example() {
    $entity_manager = Drupal::service('entity_type.manager');
  }

  /**
   * Example of using a method directly on the service.
   */
  public function method_on_service() {
    $definitions = Drupal::service('entity_type.manager')->getDefinitions();
  }

  /**
   * Example of using a method that is not in entityTypeManager.
   *
   * These should now use the `entity_type.repository` service.
   */
  public function method_not_in_entityTypeManager() {
    $group = FALSE;

    $entity_manager = Drupal::service('entity_type.repository')->getEntityTypeLabels($group);
  }

  /**
   * Example of storing the services and then calling methods not in entityTypeManager.
   *
   * These should now use the `entity_type.repository` service.
   */
  public function stored_service_and_method_not_in_entityTypeManager() {
    /* @var $entity_manager \Drupal\Core\Entity\EntityTypeRepositoryInterface */
    $entity_manager = Drupal::service('entity_type.repository');

    $group = FALSE;
    $class_name = 'MyClass';

    $entity_manager->getEntityTypeLabels($group);
    $entity_manager->getEntityTypeFromClass($class_name);
  }

}
