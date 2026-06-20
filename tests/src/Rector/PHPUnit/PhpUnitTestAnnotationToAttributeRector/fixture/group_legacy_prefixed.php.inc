<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class GroupLegacyPrefixedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group legacy-kernel
     */
    public function testThing(): void
    {
    }
}
?>
-----
<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class GroupLegacyPrefixedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group legacy-kernel
     */
    #[\PHPUnit\Framework\Attributes\Group('legacy-kernel')]
    public function testThing(): void
    {
    }
}
?>
