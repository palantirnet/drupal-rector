<?php

namespace Drupal\Tests\example\Kernel;

use Drupal\KernelTests\KernelTestBase;

class ExampleOtherModuleTest extends KernelTestBase
{
    protected function setUp(): void
    {
        parent::setUp();
        // First arg is not 'system' — leave untouched.
        $this->installSchema('node', ['sequences']);
    }
}
?>
-----
<?php

namespace Drupal\Tests\example\Kernel;

use Drupal\KernelTests\KernelTestBase;

class ExampleOtherModuleTest extends KernelTestBase
{
    protected function setUp(): void
    {
        parent::setUp();
        // First arg is not 'system' — leave untouched.
        $this->installSchema('node', ['sequences']);
    }
}
?>
