<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertEqualTest extends BrowserTestBase {

    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';

    public function testExample() {
        $this->assertEquals('Actual', 'Expected', 'Message');
        $this->assertNotEquals('Actual', 'Expected', 'Message');
    }

}
