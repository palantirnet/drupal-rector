<?php

namespace Drupal\Tests\rector_examples\Unit;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Tests\UnitTestCase;

class UnitTestCaseGetMockUpdated extends UnitTestCase {

  /**
   * A simple example using the class property.
   */
  public function simple_example() {
    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
  }

  /**
   * A simple example using a string directly.
   */
  public function class_name_as_string() {
    $this->entityTypeManager = $this->createMock('Drupal\Core\Entity\EntityTypeManagerInterface');
  }
}
