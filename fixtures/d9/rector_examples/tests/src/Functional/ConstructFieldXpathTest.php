<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class ConstructFieldXpathTest extends BrowserTestBase {

    public function testExample() {
        $this->drupalGet('/form-test/select');
        $this->constructFieldXpath('id', 'edit-preferred-admin-langcode');
    }

}
