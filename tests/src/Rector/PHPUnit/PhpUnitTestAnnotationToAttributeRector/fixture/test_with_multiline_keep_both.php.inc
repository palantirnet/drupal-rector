<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class TestWithTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testWith [1, 2]
     *           [3, 4]
     */
    public function testThing($a, $b): void
    {
    }
}
?>
-----
<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class TestWithTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testWith [1, 2]
     *           [3, 4]
     */
    #[\PHPUnit\Framework\Attributes\TestWith([1, 2])]
    #[\PHPUnit\Framework\Attributes\TestWith([3, 4])]
    public function testThing($a, $b): void
    {
    }
}
?>
