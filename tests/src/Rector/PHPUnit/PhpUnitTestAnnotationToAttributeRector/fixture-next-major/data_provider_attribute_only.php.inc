<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture_next_major;

final class DataProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideThings
     */
    public function testThing($value): void
    {
    }

    public static function provideThings(): array
    {
        return [];
    }
}
?>
-----
<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture_next_major;

final class DataProviderTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideThings')]
    public function testThing($value): void
    {
    }

    public static function provideThings(): array
    {
        return [];
    }
}
?>
