<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertTest extends BrowserTestBase {

    public function testExample() {
        $foo = TRUE;
        $this->assert($foo);
    }

}
