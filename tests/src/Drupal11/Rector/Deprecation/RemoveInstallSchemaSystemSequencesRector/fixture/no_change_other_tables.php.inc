<?php

namespace Drupal\Tests\example\Kernel;

use Drupal\KernelTests\KernelTestBase;

class ExampleOtherTablesTest extends KernelTestBase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Array does not contain 'sequences' — leave untouched.
        $this->installSchema('system', ['router', 'queue']);
    }
}
?>
-----
<?php

namespace Drupal\Tests\example\Kernel;

use Drupal\KernelTests\KernelTestBase;

class ExampleOtherTablesTest extends KernelTestBase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Array does not contain 'sequences' — leave untouched.
        $this->installSchema('system', ['router', 'queue']);
    }
}
?>
