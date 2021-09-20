<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertIdenticalObjectTest extends BrowserTestBase {

    public function testExample() {
        $this->assertIdenticalObject('Actual', 'Expected', 'Message');
    }

}
