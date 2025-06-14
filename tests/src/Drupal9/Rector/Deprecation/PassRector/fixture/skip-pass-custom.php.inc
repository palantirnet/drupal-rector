<?php

namespace Drupal9\Rector\Deprecation\PassRector\fixture;

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    protected function pass(string $message) {
        // Overridden.
    }

    /**
     * A simple example using the class property.
     */
    public function example() {
        $this->pass('The whole transaction is rolled back when a duplicate key insert occurs.');
    }
}
