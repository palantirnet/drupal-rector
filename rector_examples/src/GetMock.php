<?php

/**
 * @file
 * Contains \Drupal\rector_examples\GetMock.
 */

namespace Drupal\rector_examples;

class GetMock {

  /**
   * Example of static method calls from a class.
   *
   * @return null
   */
  public function example() {
    $obj = new \stdClass();
    $paramObj = new \stdClass();

    $obj->getMock($paramObj);

    return NULL;
  }

}
