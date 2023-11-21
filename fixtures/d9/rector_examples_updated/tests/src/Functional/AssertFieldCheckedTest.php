<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertFieldCheckedTest extends BrowserTestBase {

    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';

    public function testFieldChecked() {
        $this->assertSession()->checkboxChecked('edit-settings-view-mode', 'default');
    }

    public function testNoFieldChecked() {
        $this->assertSession()->checkboxNotChecked('edit-settings-view-mode', 'default');
    }

}
