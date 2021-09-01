<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertFieldCheckedTest extends BrowserTestBase {

    public function testExample() {
        $this->assertSession()->checkboxChecked('edit-settings-view-mode', 'default');
    }

}
