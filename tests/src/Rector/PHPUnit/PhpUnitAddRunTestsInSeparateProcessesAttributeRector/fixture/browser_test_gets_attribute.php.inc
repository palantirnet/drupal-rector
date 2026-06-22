<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitAddRunTestsInSeparateProcessesAttributeRector\fixture;

use Drupal\Tests\BrowserTestBase;

final class SomeBrowserTest extends BrowserTestBase
{
    public function testThing(): void
    {
    }
}
?>
-----
<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitAddRunTestsInSeparateProcessesAttributeRector\fixture;

use Drupal\Tests\BrowserTestBase;

#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
final class SomeBrowserTest extends BrowserTestBase
{
    public function testThing(): void
    {
    }
}
?>
