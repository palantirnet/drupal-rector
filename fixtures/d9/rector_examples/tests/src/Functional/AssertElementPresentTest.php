<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertElementPresentTest extends BrowserTestBase {

    public function testAssertElementPresent() {
        $this->assertElementPresent('css', '.region-content-message.region-empty');
        $this->assertElementNotPresent('css', '.region-content-message.region-empty');
    }

}
