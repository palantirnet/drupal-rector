<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $xpath = $this->buildXPathQuery('//select[@name=:name]', [':name' => $name]);
        $fields = $this->xpath($xpath);
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
        $xpath = $this->assertSession()->buildXPathQuery('//select[@name=:name]', [':name' => $name]);
        $fields = $this->xpath($xpath);
    }
}
?>
