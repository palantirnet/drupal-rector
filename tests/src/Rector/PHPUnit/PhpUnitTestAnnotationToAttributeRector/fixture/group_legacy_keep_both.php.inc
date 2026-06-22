<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class GroupLegacyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group legacy
     */
    public function testDeprecatedThing(): void
    {
    }
}
?>
-----
<?php

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector\fixture;

final class GroupLegacyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group legacy
     */
    #[\PHPUnit\Framework\Attributes\IgnoreDeprecations]
    public function testDeprecatedThing(): void
    {
    }
}
?>
