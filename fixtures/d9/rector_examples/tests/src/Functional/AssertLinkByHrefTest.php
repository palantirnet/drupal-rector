<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertLinkByHrefTest extends BrowserTestBase {

    public function testLinkByHref() {
        $this->assertLinkByHref('user/1/translations');
    }

    public function testNoLinkByHref() {
        $this->assertNoLinkByHref('user/2/translations');
    }

}
