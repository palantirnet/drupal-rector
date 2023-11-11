<?php

namespace Drupal9\Rector\Deprecation\AssertNoFieldByNameRector\fixture;

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $this->assertNoFieldByName('name');
        $this->assertNoFieldByName('notexisting');
    }
}
?>
-----
<?php

namespace Drupal9\Rector\Deprecation\AssertNoFieldByNameRector\fixture;

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $this->assertSession()->fieldValueNotEquals('name', '');
        $this->assertSession()->fieldValueNotEquals('notexisting', '');
    }
}
?>
