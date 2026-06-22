<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitAddRunTestsInSeparateProcessesAttributeRector\fixture;

use Drupal\KernelTests\KernelTestBase;

final class SomeKernelTest extends KernelTestBase
{
    public function testThing(): void
    {
    }
}
?>
-----
<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitAddRunTestsInSeparateProcessesAttributeRector\fixture;

use Drupal\KernelTests\KernelTestBase;

#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
final class SomeKernelTest extends KernelTestBase
{
    public function testThing(): void
    {
    }
}
?>
