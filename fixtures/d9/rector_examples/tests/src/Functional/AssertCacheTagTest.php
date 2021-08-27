<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertCacheTagTest extends BrowserTestBase {

    public function testAssertCacheTag() {
        $this->drupalGet('');
        $this->assertCacheTag('config:block_list');
        $this->assertText('Powered by Drupal');
    }

    public function testAssertNoCacheTag() {
        $this->drupalGet('');
        $this->assertNoCacheTag('config:block_list');
    }

}
