<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $this->assertNoFieldByName('name', 'not the value');
        $this->assertNoFieldByName('notexisting', NULL);
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
        $this->assertSession()->fieldValueNotEquals('name', 'not the value');
        $this->assertSession()->fieldNotExists('notexisting');
    }
}
?>
