<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertOptionTest extends BrowserTestBase {

    public function testExample() {
        $this->assertOption('edit-settings-view-mode', 'default');
        $this->assertNoOption('edit-settings-view-mode', 'default');
        $this->assertOptionByText('edit-settings-view-mode', 'default');
        $this->assertOptionSelected('options', 2);
    }

}
