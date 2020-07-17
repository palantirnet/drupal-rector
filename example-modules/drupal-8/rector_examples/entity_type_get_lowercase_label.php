<?php

/**
 * EntityType::getSingularLabel() now handles lower-casing the entity type label; getLowercaseLabel() deprecated.
 */

/**
 * A simple example.
 */
function simple_example() {
  /* @var \Drupal\node\Entity\Node $node */
  $node = \Drupal::entityTypeManager()->getStorage('node')->load(123);
  $entity_type = $node->getEntityType();
  $entity_type->getLowercaseLabel();
}

/**
 * A chained example.
 *
 * Seems like we should support this, but the method chaining errors out.
 */
function chained_example() {
  /* @var \Drupal\node\Entity\Node $node */
  $node = \Drupal::entityTypeManager()->getStorage('node')->load(123);
  $label = $node->getEntityType()->getLowercaseLabel();
}
