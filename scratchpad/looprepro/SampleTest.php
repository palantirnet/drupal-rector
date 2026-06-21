<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @group example
 */
#[Group('example')]
final class SampleLoopTest extends TestCase
{
    public function testThing(): void
    {
    }
}
