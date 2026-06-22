<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

/**
 * @group example
 */
#[Group('example')]
final class GroupIdempotentUnimportedTest extends \PHPUnit\Framework\TestCase
{
    public function testThing(): void
    {
    }
}
