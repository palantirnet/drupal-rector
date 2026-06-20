<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class DependsModifierTest extends \PHPUnit\Framework\TestCase
{
    public function testFirst(): void
    {
    }

    /**
     * @depends clone testFirst
     */
    public function testSecond(): void
    {
    }
}
