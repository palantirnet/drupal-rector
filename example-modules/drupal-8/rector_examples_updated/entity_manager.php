<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * Simple example
 */
function simple_example() {
  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // We are assuming that we want to use the `entity_type.manager` service since no method was called here directly. Please confirm this is the case. See https://www.drupal.org/node/2549139 for more information.
  $entity_manager = \Drupal::service('entity_type.manager');
}

/**
 * Example of using a method directly on the service.
 */
function method_on_service() {
  $definitions = \Drupal::service('entity_type.manager')->getDefinitions();
}

/**
 * Example of using a method that is not in entityTypeManager.
 *
 * These should now use the `entity_type.repository` service.
 */
function method_not_in_entityTypeManager() {
  $group = FALSE;

  $entity_manager = \Drupal::service('entity_type.repository')->getEntityTypeLabels($group);
}

/**
 * Example of storing the services and then calling methods not in entityTypeManager.
 *
 * These should now use the `entity_type.repository` service.
 */
function stored_service_and_method_not_in_entityTypeManager() {
  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // We are assuming that we want to use the `entity_type.manager` service since no method was called here directly. Please confirm this is the case. See https://www.drupal.org/node/2549139 for more information.
  $entity_manager = \Drupal::service('entity_type.manager');

  $group = FALSE;
  $class_name = 'MyClass';

  $entity_manager->getEntityTypeLabels($group);
  $entity_manager->getEntityTypeFromClass($class_name);
}
