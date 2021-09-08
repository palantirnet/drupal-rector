<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertUniqueTextTest extends BrowserTestBase {

    public function testAssertText() {
        $this->assertUniqueText('Color set');
        $this->assertNoUniqueText('Duplicated message');
    }

}
