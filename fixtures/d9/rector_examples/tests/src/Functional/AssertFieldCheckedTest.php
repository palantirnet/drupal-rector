<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertFieldCheckedTest extends BrowserTestBase {

    public function testFieldChecked() {
        $this->assertFieldChecked('edit-settings-view-mode', 'default');
    }

    public function testNoFieldChecked() {
        $this->assertNoFieldChecked('edit-settings-view-mode', 'default');
    }

}
