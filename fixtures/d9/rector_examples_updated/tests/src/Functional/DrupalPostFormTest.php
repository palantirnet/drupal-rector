<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function simple_example() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
        // You must call `$this->drupalGet("admin/config/system/actions");" before submitForm
        $this->submitForm($edit, 'Create');
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * Passing the options as a direct value.
     */
    public function with_options_direct() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        $this->drupalPostForm('admin/config/system/actions', $edit, 'Create', ['foo' => 'bar']);
        // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
        // You must call `$this->drupalGet("admin/config/system/actions", ['foo' => 'bar']);" before submitForm
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
        // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
        // You must call `$this->drupalGet("admin/config/system/actions", $options);" before submitForm
        $this->submitForm($edit, 'Create');
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * Passing the options as a direct value.
     */
    public function with_html_id() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
        // You must call `$this->drupalGet("admin/config/system/actions", []);" before submitForm
        $this->submitForm($edit, 'Create', 'foo_bar_baz');
        $this->assertSession()->statusCodeEquals(200);
    }
}
