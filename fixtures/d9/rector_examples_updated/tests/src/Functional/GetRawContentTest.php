<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class GetRawContentTest extends BrowserTestBase {

    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';

    public function testExample() {
        $this->drupalGet('/form-test/select');
        $this->getSession()->getPage()->getContent();
    }

}
