<?php

namespace Rector\Tests\DrupalRector\Rector\Class_\FunctionalTestDefaultThemePropertyRector\Fixture;

use Drupal\Tests\BrowserTestBase;

class SomeClassTest extends BrowserTestBase {
}

?>
-----
<?php

namespace Rector\Tests\DrupalRector\Rector\Class_\FunctionalTestDefaultThemePropertyRector\Fixture;

use Drupal\Tests\BrowserTestBase;

class SomeClassTest extends BrowserTestBase
{
    protected $defaultTheme = 'stark';
}

?>
