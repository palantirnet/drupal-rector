<?php

namespace Drupal\Tests\rector_examples\Kernel;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\KernelTests\KernelTestBase;

class KernelTestBaseGetMock extends KernelTestBase {

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
}
