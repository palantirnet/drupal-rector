<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertLinkTest extends BrowserTestBase {

    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';

    public function testLink() {
        $this->assertSession()->linkExists('Anonymous comment title');
    }

    public function testNoLink() {
        $this->assertSession()->linkNotExists('Anonymous comment title');
    }


}
