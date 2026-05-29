<?php

namespace Drupal\Tests\example;

use PHPUnit\Framework\TestCase;

trait SomeOtherTrait
{
}

class ExampleOtherTest extends TestCase
{
    use SomeOtherTrait;

    public function testSomething(): void
    {
    }
}
?>
-----
<?php

namespace Drupal\Tests\example;

use PHPUnit\Framework\TestCase;

trait SomeOtherTrait
{
}

class ExampleOtherTest extends TestCase
{
    use SomeOtherTrait;

    public function testSomething(): void
    {
    }
}
?>
