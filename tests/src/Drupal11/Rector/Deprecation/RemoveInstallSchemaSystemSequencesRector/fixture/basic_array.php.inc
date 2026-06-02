<?php

namespace Drupal\Tests\example\Kernel;

use Drupal\KernelTests\KernelTestBase;

class ExampleSequencesArrayTest extends KernelTestBase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->installSchema('system', ['sequences']);
    }
}
?>
-----
<?php

namespace Drupal\Tests\example\Kernel;

use Drupal\KernelTests\KernelTestBase;

class ExampleSequencesArrayTest extends KernelTestBase
{
    protected function setUp(): void
    {
        parent::setUp();
    }
}
?>
