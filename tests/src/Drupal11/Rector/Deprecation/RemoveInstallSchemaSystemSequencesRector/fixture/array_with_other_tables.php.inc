<?php

namespace Drupal\Tests\example\Kernel;

use Drupal\KernelTests\KernelTestBase;

class ExampleSequencesMixedTest extends KernelTestBase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->installSchema('system', ['sequences', 'router']);
    }
}
?>
-----
<?php

namespace Drupal\Tests\example\Kernel;

use Drupal\KernelTests\KernelTestBase;

class ExampleSequencesMixedTest extends KernelTestBase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->installSchema('system', ['router']);
    }
}
?>
