<?php

namespace Drupal\Tests\rector_examples\Unit;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Tests\UnitTestCase;

class UnitTestCaseGetMock extends UnitTestCase {

  /**
   * A simple example using the class property.
   */
  public function simple_example() {
    $this->entityTypeManager = $this->getMock(EntityTypeManagerInterface::class);
  }

  /**
   * A simple example using a string directly.
   */
  public function class_name_as_string() {
    $this->entityTypeManager = $this->getMock('Drupal\Core\Entity\EntityTypeManagerInterface');
  }

  /**
   * An example of chaining method calls.
   *
   * This will be updated by Rector PHPUnit.
   */
  public function chaining_method_calls() {
    $this->entityTypeManager = $this->getMockBuilder('Drupal\Core\Entity\EntityTypeManagerInterface')
      ->disableOriginalConstructor()
      ->getMock();
  }
}
