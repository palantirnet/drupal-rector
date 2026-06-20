<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class GroupIdempotentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group example
     */
    #[\PHPUnit\Framework\Attributes\Group('example')]
    public function testThing(): void
    {
    }
}
