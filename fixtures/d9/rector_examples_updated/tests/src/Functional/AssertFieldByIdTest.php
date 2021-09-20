<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertFieldByIdTest extends BrowserTestBase {

    public function testFieldById() {
        $this->assertSession()->fieldExists('edit-name');
        $this->assertSession()->fieldValueEquals('edit-name', 'Test name');
        $this->assertSession()->fieldExists('edit-description');
        $this->assertSession()->fieldValueEquals('edit-description', '');
    }

    public function testNoFieldById() {
        $this->assertSession()->fieldValueNotEquals('name', '');
        $this->assertSession()->fieldValueNotEquals('name', 'not the value');
        $this->assertSession()->fieldValueNotEquals('notexisting', '');
        $this->assertSession()->fieldNotExists('notexisting');
    }

}
