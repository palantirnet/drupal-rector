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
  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // Please confirm that `$entity_type` is an instance of `\Drupal\Core\Entity\EntityType`. Only the method name and not the class name was checked for this replacement, so this may be a false positive.
  $entity_type->getSingularLabel();
}

/**
 * A chained example.
 *
 * Seems like we should support this, but the method chaining errors out.
 */
function chained_example() {
  /* @var \Drupal\node\Entity\Node $node */
  $node = \Drupal::entityTypeManager()->getStorage('node')->load(123);
  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // Please confirm that `getEntityType()` is an instance of `\Drupal\Core\Entity\EntityType`. Only the method name and not the class name was checked for this replacement, so this may be a false positive.
  $label = $node->getEntityType()->getSingularLabel();
}
