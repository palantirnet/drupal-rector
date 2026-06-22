<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitAddRunTestsInSeparateProcessesAttributeRector\fixture;

use Drupal\KernelTests\KernelTestBase;

#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
final class AlreadyAttributedTest extends KernelTestBase
{
    public function testThing(): void
    {
    }
}
