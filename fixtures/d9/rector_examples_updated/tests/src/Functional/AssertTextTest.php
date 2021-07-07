<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertTextTest extends BrowserTestBase {

    public function testAssertText() {
        $current_content = $this->randomMachineName();
        $this->drupalGet('test-page');
        // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
        // Verify the assertion: pageTextContains() for HTML responses, responseContains() for non-HTML responses.
        // The passed text should be HTML decoded, exactly as a human sees it in the browser.
        $this->assertSession()->pageTextContains($current_content);
    }

}
