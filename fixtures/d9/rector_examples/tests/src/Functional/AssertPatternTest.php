<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertPatternTest extends BrowserTestBase {

    public function testExample() {
        $this->assertPattern('|<h4[^>]*></h4>|', 'No empty H4 element found.');
        $this->assertNoPattern('|<h4[^>]*></h4>|', 'No empty H4 element found.');
    }

}
