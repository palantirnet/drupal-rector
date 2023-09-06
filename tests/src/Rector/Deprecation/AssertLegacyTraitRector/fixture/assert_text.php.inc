<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $current_content = $this->randomMachineName();
        $this->drupalGet('test-page');
        $this->assertNoText($current_content, 'Block content displays on the test page.');
    }
}
?>
-----
<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $current_content = $this->randomMachineName();
        $this->drupalGet('test-page');
        // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
        // Verify the assertion: pageTextNotContains() for HTML responses, responseNotContains() for non-HTML responses.
        // The passed text should be HTML decoded, exactly as a human sees it in the browser.
        $this->assertSession()->pageTextNotContains($current_content);
    }
}
?>
