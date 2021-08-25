<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertElementPresentTest extends BrowserTestBase {

    public function testAssertElementPresent() {
        $this->assertSession()->elementExists('css', '.region-content-message.region-empty');
        $this->assertSession()->elementNotExists('css', '.region-content-message.region-empty');
    }

}
