<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertIdenticalObjectTest extends BrowserTestBase {

    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';

    public function testExample() {
        $this->assertEquals('Actual', 'Expected', 'Message');
    }

}
