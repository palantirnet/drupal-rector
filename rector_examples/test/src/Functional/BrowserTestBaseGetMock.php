<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

  /**
   *
   */
  public function simple_example() {
      $this->entityTypeManager = $this->getMock(EntityTypeManagerInterface::class);
  }

  /**
   *
   */
  public function class_name_as_string() {
    $this->entityTypeManager = $this->getMock('Drupal\Core\Entity\EntityTypeManagerInterface');
  }
}
