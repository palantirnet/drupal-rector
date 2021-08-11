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
        // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
        // Verify the assertion: buttonNotExists() if this is for a button.
        $this->assertSession()->fieldNotExists('notexisting');
    }
}
?>
