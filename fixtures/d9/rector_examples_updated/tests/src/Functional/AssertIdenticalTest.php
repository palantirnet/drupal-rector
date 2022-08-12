<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertIdenticalTest extends BrowserTestBase {

    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';

    public function testExample() {
        $this->assertSame('Actual', 'Expected', 'Message');
        $this->assertNotSame('Actual', 'Expected', 'Message');
    }

}
