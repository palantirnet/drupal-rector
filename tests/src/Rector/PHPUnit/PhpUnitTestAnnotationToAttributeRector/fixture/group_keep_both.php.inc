<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class GroupKeepBothTest extends \PHPUnit\Framework\TestCase
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

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class GroupKeepBothTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group example
     */
    #[\PHPUnit\Framework\Attributes\Group('example')]
    public function testThing(): void
    {
    }
}
?>
