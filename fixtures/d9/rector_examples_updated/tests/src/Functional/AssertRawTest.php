<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertRawTest extends BrowserTestBase {

    public function testExample() {
        $this->assertSession()->responseContains('bartik/logo.svg');
        $this->assertSession()->responseNotContains('bartik/logo.svg');
    }

}
