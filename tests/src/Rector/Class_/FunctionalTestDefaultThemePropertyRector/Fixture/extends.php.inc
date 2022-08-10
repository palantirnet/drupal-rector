<?php

namespace Rector\Tests\DrupalRector\Rector\Class_\FunctionalTestDefaultThemePropertyRector\Fixture;

use Drupal\Tests\BrowserTestBase;

abstract class Foo extends BrowserTestBase {
    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';
}

class SomeClassTest extends Foo {

    public function simple_example() {
        $this->assertTrue(TRUE);
    }

}

?>
-----
<?php

namespace Rector\Tests\DrupalRector\Rector\Class_\FunctionalTestDefaultThemePropertyRector\Fixture;

use Drupal\Tests\BrowserTestBase;

abstract class Foo extends BrowserTestBase {
    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';
}

class SomeClassTest extends Foo {

    public function simple_example() {
        $this->assertTrue(TRUE);
    }

}

?>
