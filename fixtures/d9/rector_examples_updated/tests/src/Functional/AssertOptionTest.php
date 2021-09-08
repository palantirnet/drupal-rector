<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertOptionTest extends BrowserTestBase {

    public function testExample() {
        $this->assertSession()->optionExists('edit-settings-view-mode', 'default');
        $this->assertSession()->optionNotExists('edit-settings-view-mode', 'default');
        $this->assertSession()->optionExists('edit-settings-view-mode', 'default');
        $this->assertTrue($this->assertSession()->optionExists('options', 2)->hasAttribute('selected'));
    }

}
