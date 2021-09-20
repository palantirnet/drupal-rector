<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertFieldByNameTest extends BrowserTestBase {

    public function testFieldByName() {
        $this->assertFieldByName('field_name', 'expected_value');
        $this->assertFieldByName("field_name[0][value][date]", '', 'Date element found.');
        $this->assertFieldByName("field_name[0][value][time]", NULL, 'Time element found.');
    }

    public function testNoFieldByName() {
        $this->assertNoFieldByName('name');
        $this->assertNoFieldByName('name', 'not the value');
        $this->assertNoFieldByName('notexisting');
        $this->assertNoFieldByName('notexisting', NULL);
    }

}
