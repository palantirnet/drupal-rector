<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $to_check = 'Duplicated message';
        $this->assertNoUniqueText($to_check);
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
        $to_check = 'Duplicated message';
        $page_text = $this->getSession()->getPage()->getText();
        $nr_found = substr_count($page_text, $to_check);
        $this->assertGreaterThan(1, $nr_found, "'{$to_check}' found more than once on the page");
    }
}
?>
