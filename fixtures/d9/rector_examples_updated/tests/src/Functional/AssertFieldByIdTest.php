<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class AssertFieldByIdTest extends BrowserTestBase {

    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';

    public function testFieldById() {
        $this->assertSession()->fieldExists('edit-name');
        $this->assertSession()->fieldValueEquals('edit-name', 'Test name');
        $this->assertSession()->fieldExists('edit-description');
        $this->assertSession()->fieldValueEquals('edit-description', '');
    }

    public function testNoFieldById() {
        $this->assertSession()->fieldNotExists('name');
        $this->assertSession()->fieldValueNotEquals('name', 'not the value');
        $this->assertSession()->fieldNotExists('notexisting');
        $this->assertSession()->fieldNotExists('notexisting');
    }

}
