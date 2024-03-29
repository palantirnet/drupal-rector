<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class ConstructFieldXpathTest extends BrowserTestBase {

    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';

    public function testExample() {
        $this->drupalGet('/form-test/select');
        $this->getSession()->getPage()->findField('edit-preferred-admin-langcode');
    }

}
