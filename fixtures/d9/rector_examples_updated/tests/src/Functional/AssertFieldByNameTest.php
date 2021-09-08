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
        // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
        // Verify the assertion: buttonNotExists() if this is for a button.
        $this->assertSession()->fieldNotExists('notexisting');
    }

}
