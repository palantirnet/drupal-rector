<?php

namespace Drupal\Tests\example;

class ExampleTest
{
    /**
     * Tests deprecated behaviour.
     *
     * @covers ::foo
     * @group legacy
     */
    public function testLegacyBehavior(): void
    {
    }
}

?>
-----
<?php

namespace Drupal\Tests\example;

class ExampleTest
{
    /**
     * Tests deprecated behaviour.
     *
     * @covers ::foo
     */
    #[\PHPUnit\Framework\Attributes\IgnoreDeprecations]
    public function testLegacyBehavior(): void
    {
    }
}

?>
