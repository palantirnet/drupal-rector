<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $this->drupalGet('test-page');
        $this->assertNoRaw('bartik/logo.svg', 'Make sure the original bartik logo exists.');
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
        $this->drupalGet('test-page');
        $this->assertSession()->responseNotContains('bartik/logo.svg');
    }
}
?>
