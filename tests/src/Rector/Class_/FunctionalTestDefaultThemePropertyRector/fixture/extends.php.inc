<?php

namespace Rector\Tests\DrupalRector\Rector\Class_\FunctionalTestDefaultThemePropertyRector\fixture;

use Drupal\Tests\BrowserTestBase;

abstract class Foo extends BrowserTestBase {

    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';
}

class FooTest extends Foo {

    public function simple_example() {
        $this->assertTrue(TRUE);
    }

}

?>
-----
<?php

namespace Rector\Tests\DrupalRector\Rector\Class_\FunctionalTestDefaultThemePropertyRector\fixture;

use Drupal\Tests\BrowserTestBase;

abstract class Foo extends BrowserTestBase {

    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';
}

class FooTest extends Foo {

    public function simple_example() {
        $this->assertTrue(TRUE);
    }

}

?>
