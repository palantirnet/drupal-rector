<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class DrupalPostFormTest extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function simple_example() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        $this->drupalPostForm('admin/config/system/actions', $edit, 'Create');
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * Passing the options as a direct value.
     */
    public function with_options_direct() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        $this->drupalPostForm('admin/config/system/actions', $edit, 'Create', ['foo' => 'bar']);
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * Passing the options as a direct value.
     */
    public function with_options_as_variable() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        $options = ['foo' => 'bar'];
        $this->drupalPostForm('admin/config/system/actions', $edit, 'Create', $options);
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * Passing the options as a direct value.
     */
    public function with_html_id() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        $this->drupalPostForm('admin/config/system/actions', $edit, 'Create', [], 'foo_bar_baz');
        $this->assertSession()->statusCodeEquals(200);
    }
}
