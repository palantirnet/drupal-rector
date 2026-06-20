<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitAddRunTestsInSeparateProcessesAttributeRector\fixture;

use Drupal\KernelTests\KernelTestBase;

abstract class SomeAbstractTestBase extends KernelTestBase
{
    public function testThing(): void
    {
    }
}
