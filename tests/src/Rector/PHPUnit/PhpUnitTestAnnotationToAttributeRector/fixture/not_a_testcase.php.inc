<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class NotATest
{
    /**
     * @group example
     */
    public function doThing(): void
    {
    }
}
