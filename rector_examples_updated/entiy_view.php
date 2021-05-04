<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  $entity = new stdClass();
  $view = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId())->view($entity, 'default');
}

/**
 * An example using all of the arguments.
 */
function lagncode_example() {
  $entity = new stdClass();
  $langcode = 'de';
  $view = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId())->view($entity, 'default', $langcode);
}

/**
 * An example using all of the arguments as variables.
 */
function arguments_member_values() {
  $entity = new stdClass();
  $view = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId())->view($entity, $entity->field_view_mode);
}
