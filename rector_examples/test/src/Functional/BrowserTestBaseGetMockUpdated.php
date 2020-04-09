<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMockUpdated extends BrowserTestBase {

  /**
   *
   */
  public function simple_example() {
      $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
  }

  /**
   *
   */
  public function class_name_as_string() {
    $this->entityTypeManager = $this->createMock('Drupal\Core\Entity\EntityTypeManagerInterface');
  }
}
