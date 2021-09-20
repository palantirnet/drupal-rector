<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertResponseTest extends BrowserTestBase {

    public function testExample() {
        $this->assertResponse(200);
    }

}
