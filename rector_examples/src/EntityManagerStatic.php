<?php

namespace Drupal\rector_examples;

class EntityManagerStatic {

  /**
   * Example of static method calls from a class.
   *
   * @return null
   */
  public function example() {
    $entity_manager = \Drupal::entityManager();

    return NULL;
  }

}
