<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertIdenticalTest extends BrowserTestBase {

    public function testExample() {
        $this->assertSame('Actual', 'Expected', 'Message');
        $this->assertNotSame('Actual', 'Expected', 'Message');
    }

}
