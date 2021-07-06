<?php

/**
 * This demonstrates the deprecated static calls that might be called from procedural code like `.module` files.
 */

/**
 * A simple example.
 */
function simple_example() {
  /* @var \Drupal\node\Entity\Node $node */
  $node = \Drupal::entityTypeManager()->getStorage('node')->load(123);

  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // Please confirm that `$node` is an instance of `\Drupal\Core\Entity\EntityInterface`. Only the method name and not the class name was checked for this replacement, so this may be a false positive.
  $link = $node->toLink()->toString();
}

/**
 * An example using arguments.
 */
function example_using_arguments() {
  /* @var \Drupal\node\Entity\Node $node */
  $node = \Drupal::entityTypeManager()->getStorage('node')->load(123);

  // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
  // Please confirm that `$node` is an instance of `\Drupal\Core\Entity\EntityInterface`. Only the method name and not the class name was checked for this replacement, so this may be a false positive.
  $link = $node->toLink('Hello world', 'canonical', ['absolute' => TRUE])->toString();
}
