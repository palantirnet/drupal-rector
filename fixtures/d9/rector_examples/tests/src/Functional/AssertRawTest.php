<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertRawTest extends BrowserTestBase {

    public function testExample() {
        $this->assertRaw('bartik/logo.svg');
        $this->assertNoRaw('bartik/logo.svg');
    }

}
