<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertOptionTest extends BrowserTestBase {

    public function testAssertElementPresent() {
        $this->assertOption('edit-settings-view-mode', 'default');
        $this->assertNoOption('edit-settings-view-mode', 'default');
    }

}
