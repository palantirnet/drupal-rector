<?php

namespace Rector\Tests\DrupalRector\Rector\Class_\FunctionalTestDefaultThemePropertyRector\fixture;

use Drupal\Tests\BrowserTestBase;

class SomeClassTest extends BrowserTestBase {

    public function simple_example() {
        $this->assertTrue(TRUE);
    }

}

?>
-----
<?php

namespace Rector\Tests\DrupalRector\Rector\Class_\FunctionalTestDefaultThemePropertyRector\fixture;

use Drupal\Tests\BrowserTestBase;

class SomeClassTest extends BrowserTestBase {

    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';

    public function simple_example() {
        $this->assertTrue(TRUE);
    }

}

?>
