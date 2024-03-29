<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class DrupalPostFormTest extends BrowserTestBase {

    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';

    /**
     * A simple example using the class property.
     */
    public function simple_example() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        $this->drupalGet('admin/config/system/actions');
        $this->submitForm($edit, 'Create');
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * Passing the options as a direct value.
     */
    public function with_options_direct() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        $this->drupalGet('admin/config/system/actions', ['foo' => 'bar']);
        $this->submitForm($edit, 'Create');
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * Passing the options as a direct value.
     */
    public function with_options_as_variable() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        $options = ['foo' => 'bar'];
        $this->drupalGet('admin/config/system/actions', $options);
        $this->submitForm($edit, 'Create');
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * Passing the options as a direct value.
     */
    public function with_html_id() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        $this->drupalGet('admin/config/system/actions', []);
        $this->submitForm($edit, 'Create', 'foo_bar_baz');
        $this->assertSession()->statusCodeEquals(200);
    }
}
