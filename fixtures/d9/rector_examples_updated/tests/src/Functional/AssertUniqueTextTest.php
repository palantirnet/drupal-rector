<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertUniqueTextTest extends BrowserTestBase {

    public function testAssertText() {
        $this->assertSession()->pageTextContainsOnce('Color set');
        $page_text = $this->getSession()->getPage()->getText();
        $nr_found = substr_count($page_text, 'Duplicated message');
        $this->assertGreaterThan(1, $nr_found, "'Duplicated message' found more than once on the page");
    }

}
