<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertFieldByNameTest extends BrowserTestBase {

    public function testFieldByName() {
        $this->assertSession()->fieldValueEquals('field_name', 'expected_value');
        $this->assertSession()->fieldValueEquals("field_name[0][value][date]", '');
        $this->assertSession()->fieldExists("field_name[0][value][time]");
    }

    public function testNoFieldByName() {
        $this->assertSession()->fieldValueNotEquals('name', '');
        $this->assertSession()->fieldValueNotEquals('name', 'not the value');
        $this->assertSession()->fieldValueNotEquals('notexisting', '');
        $this->assertSession()->fieldNotExists('notexisting');
    }
}
