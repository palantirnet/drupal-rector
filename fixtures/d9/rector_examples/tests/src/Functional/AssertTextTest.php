<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertTextTest extends BrowserTestBase {

    public function testAssertText() {
        $current_content = $this->randomMachineName();
        $this->drupalGet('test-page');
        $this->assertText($current_content, 'Block content displays on the test page.');
    }

}
