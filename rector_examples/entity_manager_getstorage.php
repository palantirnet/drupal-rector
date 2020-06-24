<?php

/**
 * getStorage() of class Drupal​\​Core​\​Entity​\​​EntityManager deprecated in drupal:8.0.0 and is removed from drupal:9.0.0.
 * Use Drupal​\​Core​\​Entity​\​EntityTypeManager::getStorage() instead.
 */

/**
 * A simple example.
 */
function simple_example() {
  $entity_type_manager = \Drupal::entityTypeManager();
  $node_storage = $entity_type_manager->getStorage('node');
  $node = $node_storage->load(123);

  $node = \Drupal::entityTypeManager()->getStorage('node')->load(123);



  \Drupal::entityManager()->getStorage()
  /* @var \Drupal\node\Entity\Node $node */
  $node = \Drupal::entityTypeManager()->getStorage('node')->load(123);
  $entity_type = $node->getEntityType();
  $entity_type->getLowercaseLabel();
}
