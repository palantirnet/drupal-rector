<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertFieldByIdTest extends BrowserTestBase {

    public function testFieldById() {
        $this->assertFieldById('edit-name', NULL);
        $this->assertFieldById('edit-name', 'Test name');
        $this->assertFieldById('edit-description', NULL);
        $this->assertFieldById('edit-description');
    }

    public function testNoFieldById() {
        $this->assertNoFieldById('name');
        $this->assertNoFieldById('name', 'not the value');
        $this->assertNoFieldById('notexisting');
        $this->assertNoFieldById('notexisting', NULL);
    }

}
