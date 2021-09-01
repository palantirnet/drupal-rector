<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertLinkByHrefTest extends BrowserTestBase {

    public function testLinkByHref() {
        $this->assertSession()->linkByHrefExists('user/1/translations');
    }

    public function testNoLinkByHref() {
        $this->assertSession()->linkByHrefNotExists('user/2/translations');
    }

}
