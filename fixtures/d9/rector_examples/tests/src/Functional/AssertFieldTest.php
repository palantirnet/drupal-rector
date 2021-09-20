<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertFieldTest extends BrowserTestBase {

    public function testField() {
        $this->assertField('files[upload]', 'Found file upload field.');
    }

    public function testNoField() {
        $this->assertNoField('files[upload]', 'Found file upload field.');
    }

}
