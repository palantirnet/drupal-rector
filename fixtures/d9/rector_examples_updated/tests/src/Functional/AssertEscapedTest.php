<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertEscapedTest extends BrowserTestBase {

    public function testAssertElementPresent() {
        $this->assertSession()->assertEscaped('Demonstrate block regions (<"Cat" & \'Mouse\'>)');
        $this->assertSession()->assertNoEscaped('<div class="escaped">');
    }

}
