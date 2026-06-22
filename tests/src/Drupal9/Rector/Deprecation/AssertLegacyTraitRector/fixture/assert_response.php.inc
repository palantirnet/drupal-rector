<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $this->drupalGet('test-page');
        $this->assertResponse(200);
        $this->assertResponse(200, 'Some message.');
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
        $this->assertSession()->statusCodeEquals(200);
        $this->assertSession()->statusCodeEquals(200);
    }
}
?>
