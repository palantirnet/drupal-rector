<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $this->drupalGet('test-page');
        $this->assertRaw('bartik/logo.svg', 'Make sure the original bartik logo exists.');
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
        $this->assertSession()->responseContains('bartik/logo.svg');
    }
}
?>
