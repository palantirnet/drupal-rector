<?php

namespace Rector\Tests\DrupalRector\Rector\Property\ProtectedStaticModulesPropertyRector\Fixture;

class SomeClassTest {
  public static $modules = [];
  public static $foo = [];
}

?>
-----
<?php

namespace Rector\Tests\DrupalRector\Rector\Property\ProtectedStaticModulesPropertyRector\Fixture;

class SomeClassTest {
  protected static $modules = [];
  public static $foo = [];
}

?>
