<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertFieldTest extends BrowserTestBase {

    public function testExample() {
        $this->assertSession()->fieldExists('files[upload]', 'Found file upload field.');
    }

}
