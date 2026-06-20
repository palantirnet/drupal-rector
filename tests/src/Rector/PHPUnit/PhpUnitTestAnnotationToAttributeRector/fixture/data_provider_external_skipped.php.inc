<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class DataProviderExternalTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider \Drupal\Tests\Some\Provider::provide
     */
    public function testThing($value): void
    {
    }
}
