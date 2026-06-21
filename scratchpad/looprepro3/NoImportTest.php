<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

use PHPUnit\Framework\TestCase;

/**
 * @group example
 */
#[Group('example')]
#[\PHPUnit\Framework\Attributes\Group('example')]
final class NoImportLoopTest extends TestCase
{
    public function testThing(): void
    {
    }
}
