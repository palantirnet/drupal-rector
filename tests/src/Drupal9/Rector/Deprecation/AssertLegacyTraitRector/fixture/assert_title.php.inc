<?php

namespace Drupal9\Rector\Deprecation\AssertLegacyTraitRector\fixture;

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

  /**
   * A simple example using the class property.
   */
  public function example() {
    $this->assertTitle('Block layout | Drupal');
  }

}

?>
-----
<?php

namespace Drupal9\Rector\Deprecation\AssertLegacyTraitRector\fixture;

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

  /**
   * A simple example using the class property.
   */
  public function example() {
    $this->assertSession()->titleEquals('Block layout | Drupal');
  }

}

?>
