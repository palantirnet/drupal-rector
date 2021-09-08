<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertIdenticalTest extends BrowserTestBase {

    public function testExample() {
        $this->assertIdentical('Actual', 'Expected', 'Message');
        $this->assertNotIdentical('Actual', 'Expected', 'Message');

    }

}
