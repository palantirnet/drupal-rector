<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * Simple example
 */
function simple_example() {
  $entity_manager = \Drupal::entityManager();
}

/**
 * Example of using a method directly on the service.
 */
function method_on_service() {
  $definitions = \Drupal::entityManager()->getDefinitions();
}

/**
 * Example of using a method that is not in entityTypeManager.
 *
 * These should now use the `entity_type.repository` service.
 */
function method_not_in_entityTypeManager() {
  $group = FALSE;

  $entity_manager = \Drupal::entityManager()->getEntityTypeLabels($group);
}

/**
 * Example of storing the services and then calling methods not in entityTypeManager.
 *
 * These should now use the `entity_type.repository` service.
 */
function stored_service_and_method_not_in_entityTypeManager() {
  $entity_manager = \Drupal::entityManager();

  $group = FALSE;
  $class_name = 'MyClass';

  $entity_manager->getEntityTypeLabels($group);
  $entity_manager->getEntityTypeFromClass($class_name);
}
