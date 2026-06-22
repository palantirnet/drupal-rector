<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitAddRunTestsInSeparateProcessesAttributeRector\fixture;

use Drupal\KernelTests\KernelTestBase;

#[RunTestsInSeparateProcesses]
final class AlreadyAttributedUnimportedTest extends KernelTestBase
{
    public function testThing(): void
    {
    }
}
