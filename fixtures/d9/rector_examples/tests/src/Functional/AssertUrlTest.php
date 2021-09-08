<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertUrlTest extends BrowserTestBase {

    public function testExample() {
        $this->assertUrl('myrootuser');
    }

}
