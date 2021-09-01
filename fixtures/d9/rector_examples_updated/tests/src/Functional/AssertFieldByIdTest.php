<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertFieldByIdTest extends BrowserTestBase {

    public function testExample() {
        $this->assertSession()->fieldValueEquals('name', '');
        $this->assertSession()->fieldValueEquals('name', 'not the value');
        $this->assertSession()->fieldValueEquals('notexisting', '');
        $this->assertSession()->fieldExists('notexisting');
    }

}
