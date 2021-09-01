<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertFieldByIdTest extends BrowserTestBase {

    public function testExample() {
        $this->assertFieldById('edit-name', NULL);
        $this->assertFieldById('edit-name', 'Test name');
        $this->assertFieldById('edit-description', NULL);
        $this->assertFieldById('edit-description');
    }

}
