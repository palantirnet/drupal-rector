<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertTitleTest extends BrowserTestBase {

    public function testExample() {
        $this->assertSession()->titleEquals('Block layout | Drupal');
    }

}
