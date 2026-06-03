<?php

namespace Drupal\Tests\example;

/**
 * Tests deprecated APIs.
 *
 * @group legacy
 */
class LegacyApiTest
{
    public function testSomething(): void
    {
    }
}

?>
-----
<?php

namespace Drupal\Tests\example;

/**
 * Tests deprecated APIs.
 */
#[\PHPUnit\Framework\Attributes\IgnoreDeprecations]
class LegacyApiTest
{
    public function testSomething(): void
    {
    }
}

?>
