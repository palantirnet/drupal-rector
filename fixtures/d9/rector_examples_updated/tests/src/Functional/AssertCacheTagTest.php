<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertCacheTagTest extends BrowserTestBase {

    public function testAssertCacheTag() {
        $this->drupalGet('');
        $this->assertSession()->responseHeaderContains('X-Drupal-Cache-Tags', 'config:block_list');
        // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
        // Verify the assertion: pageTextContains() for HTML responses, responseContains() for non-HTML responses.
        // The passed text should be HTML decoded, exactly as a human sees it in the browser.
        $this->assertSession()->pageTextContains('Powered by Drupal');
    }

    public function testAssertNoCacheTag() {
        $this->drupalGet('');
        $this->assertSession()->responseHeaderNotContains('X-Drupal-Cache-Tags', 'config:block_list');
    }

}
