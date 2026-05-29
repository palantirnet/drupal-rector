<?php

namespace Drupal\Tests\example;

use Drupal\Tests\PhpUnitCompatibilityTrait;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    use PhpUnitCompatibilityTrait;

    public function testSomething(): void
    {
    }
}
?>
-----
<?php

namespace Drupal\Tests\example;

use Drupal\Tests\PhpUnitCompatibilityTrait;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testSomething(): void
    {
    }
}
?>
