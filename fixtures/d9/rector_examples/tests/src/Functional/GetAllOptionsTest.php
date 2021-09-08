<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class GetAllOptionsTest extends BrowserTestBase {

    public function testExample() {
        $this->drupalGet('/form-test/select');
        $this->assertCount(6, $this->getAllOptions($this->cssSelect('select[name="opt_groups"]')[0]));
    }

}
