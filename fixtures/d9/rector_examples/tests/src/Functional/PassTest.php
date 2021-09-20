<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class PassTest extends BrowserTestBase {

    public function testExample() {
        $this->pass('The whole transaction is rolled back when a duplicate key insert occurs.');
    }

}
