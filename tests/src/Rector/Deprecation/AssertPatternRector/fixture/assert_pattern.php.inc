<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $this->assertPattern('|<h4[^>]*></h4>|', 'No empty H4 element found.');
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
        $this->assertSession()->responseMatches('|<h4[^>]*></h4>|', 'No empty H4 element found.');
    }
}
?>
