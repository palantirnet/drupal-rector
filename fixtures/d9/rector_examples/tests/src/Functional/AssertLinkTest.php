<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertLinkTest extends BrowserTestBase {

    public function testLink() {
        $this->assertLink('Anonymous comment title');
    }

    public function testNoLink() {
        $this->assertNoLink('Anonymous comment title');
    }

}
