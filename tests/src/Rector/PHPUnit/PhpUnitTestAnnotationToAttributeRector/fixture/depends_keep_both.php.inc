<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class DependsTest extends \PHPUnit\Framework\TestCase
{
    public function testFirst(): void
    {
    }

    /**
     * @depends testFirst
     */
    public function testSecond(): void
    {
    }
}
?>
-----
<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class DependsTest extends \PHPUnit\Framework\TestCase
{
    public function testFirst(): void
    {
    }

    /**
     * @depends testFirst
     */
    #[\PHPUnit\Framework\Attributes\Depends('testFirst')]
    public function testSecond(): void
    {
    }
}
?>
