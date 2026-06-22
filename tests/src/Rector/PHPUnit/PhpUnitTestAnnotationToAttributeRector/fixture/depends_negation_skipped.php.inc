<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class DependsNegationTest extends \PHPUnit\Framework\TestCase
{
    public function testFirst(): void
    {
    }

    /**
     * @depends !testFirst
     */
    public function testSecond(): void
    {
    }
}
