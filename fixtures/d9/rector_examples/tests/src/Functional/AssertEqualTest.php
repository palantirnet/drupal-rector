<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertEqualTest extends BrowserTestBase {

    public function testExample() {
        $this->assertEqual('Actual', 'Expected', 'Message');
        $this->assertNotEqual('Actual', 'Expected', 'Message');
    }

}
