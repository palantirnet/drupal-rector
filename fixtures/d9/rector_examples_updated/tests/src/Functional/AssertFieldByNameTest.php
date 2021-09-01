<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertFieldByNameTest extends BrowserTestBase {

    public function testExample() {
        $this->assertSession()->fieldValueEquals('field_name', 'expected_value');
        $this->assertSession()->fieldValueEquals("field_name[0][value][date]", '');
        $this->assertSession()->fieldExists("field_name[0][value][time]");
    }

}
