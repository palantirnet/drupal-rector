<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertResponseTest extends BrowserTestBase {

    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';

    public function testExample() {
        $this->assertSession()->statusCodeEquals(200);
    }

}
