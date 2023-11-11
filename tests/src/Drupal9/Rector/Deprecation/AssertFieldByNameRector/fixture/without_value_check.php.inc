<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $field_name = 'foo';
        $this->assertFieldByName("{$field_name}[0][value][date]", '', 'Date element found.');
        $this->assertFieldByName("{$field_name}[0][value][time]", null, 'Time element found.');
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
        $field_name = 'foo';
        $this->assertSession()->fieldValueEquals("{$field_name}[0][value][date]", '');
        $this->assertSession()->fieldExists("{$field_name}[0][value][time]");
    }
}
?>
