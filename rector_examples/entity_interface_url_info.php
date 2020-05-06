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

  $url = $node->urlInfo();
}

/**
 * An example using arguments.
 */
function example_using_arguments() {
  /* @var \Drupal\node\Entity\Node $node */
  $node = \Drupal::entityTypeManager()->getStorage('node')->load(123);

  $url = $node->urlInfo('edit-form', ['absolute' => TRUE]);
}
