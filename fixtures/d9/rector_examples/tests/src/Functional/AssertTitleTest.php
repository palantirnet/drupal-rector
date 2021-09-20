<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertTitleTest extends BrowserTestBase {

    public function testExample() {
        $this->assertTitle('Block layout | Drupal');
    }

}
