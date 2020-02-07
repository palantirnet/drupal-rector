<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

function example() {
  $entity_manager = \Drupal::entityManager();

  $entity_type = 'node';
  $operation = 'default';
  $handler_type = 'example';
  $class = 'MyClass';
  $definition = NULL;
  $entity_type_id = 'node';
  $exception_on_invalid = TRUE;

  $entity_manager->getAccessControlHandler($entity_type);
  $entity_manager->getStorage($entity_type);
  $entity_manager->getViewBuilder($entity_type);
  $entity_manager->getListBuilder($entity_type);
  $entity_manager->getFormObject($entity_type, $operation);
  $entity_manager->getRouteProviders($entity_type);
  $entity_manager->hasHandler($entity_type, $handler_type);
  $entity_manager->getHandler($entity_type, $handler_type);
  $entity_manager->createHandlerInstance($class, $definition);
  $entity_manager->getDefinition($entity_type_id, $exception_on_invalid);
  $entity_manager->getDefinitions();

  $entity_manager = \Drupal::entityManager();

  $group = FALSE;
  $class_name = 'MyClass';

  $entity_manager->getEntityTypeLabels($group);
  $entity_manager->getEntityTypeFromClass($class_name);
}

function updated() {
  $entity_manager = \Drupal::service('entity_type.manager');

  $entity_type = 'node';
  $operation = 'default';
  $handler_type = 'example';
  $class = 'MyClass';
  $definition = NULL;
  $entity_type_id = 'node';
  $exception_on_invalid = TRUE;

  $entity_manager->getAccessControlHandler($entity_type);
  $entity_manager->getStorage($entity_type);
  $entity_manager->getViewBuilder($entity_type);
  $entity_manager->getListBuilder($entity_type);
  $entity_manager->getFormObject($entity_type, $operation);
  $entity_manager->getRouteProviders($entity_type);
  $entity_manager->hasHandler($entity_type, $handler_type);
  $entity_manager->getHandler($entity_type, $handler_type);
  $entity_manager->createHandlerInstance($class, $definition);
  $entity_manager->getDefinition($entity_type_id, $exception_on_invalid);
  $entity_manager->getDefinitions();

  $entity_manager = \Drupal::service('entity_type.repository');

  $group = FALSE;
  $class_name = 'MyClass';

  $entity_manager->getEntityTypeLabels($group);
  $entity_manager->getEntityTypeFromClass($class_name);
}
