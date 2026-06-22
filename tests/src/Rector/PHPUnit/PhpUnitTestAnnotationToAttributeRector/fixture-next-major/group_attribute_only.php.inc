<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture_next_major;

final class GroupTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group example
     */
    public function testThing(): void
    {
    }
}
?>
-----
<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture_next_major;

final class GroupTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\Group('example')]
    public function testThing(): void
    {
    }
}
?>
