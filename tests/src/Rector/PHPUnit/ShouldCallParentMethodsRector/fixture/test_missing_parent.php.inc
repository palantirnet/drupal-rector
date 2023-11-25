<?php

namespace Drupal\Tests\Rector\Deprecation\PHPUnit\ShouldCallParentMethodsRector\fixture;

use Drupal\KernelTests\KernelTestBase;

final class MissingParentTest extends KernelTestBase {

    protected function setUp(): void
    {
        $test = 'doing things';
    }

    protected function tearDown(): void
    {
        $test = 'doing things';
    }

}

final class MissingParentInEmptyTest extends KernelTestBase {

    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

}

?>
-----
<?php

namespace Drupal\Tests\Rector\Deprecation\PHPUnit\ShouldCallParentMethodsRector\fixture;

use Drupal\KernelTests\KernelTestBase;

final class MissingParentTest extends KernelTestBase {

    protected function setUp(): void
    {
        parent::setUp();
        $test = 'doing things';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $test = 'doing things';
    }

}

final class MissingParentInEmptyTest extends KernelTestBase {

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

}

?>
