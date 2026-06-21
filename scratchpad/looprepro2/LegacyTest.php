<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\TestCase;

/**
 * @group legacy
 */
#[IgnoreDeprecations]
final class LegacyLoopTest extends TestCase
{
    public function testThing(): void
    {
    }
}
