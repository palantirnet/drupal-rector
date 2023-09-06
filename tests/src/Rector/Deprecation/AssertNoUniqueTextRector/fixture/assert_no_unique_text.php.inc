<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $this->assertNoUniqueText('Duplicated message');
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
        $page_text = $this->getSession()->getPage()->getText();
        $nr_found = substr_count($page_text, 'Duplicated message');
        $this->assertGreaterThan(1, $nr_found, "'Duplicated message' found more than once on the page");
    }
}
?>
