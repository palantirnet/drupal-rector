<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        $this->drupalPostForm('admin/config/system/actions', $edit, 'Create', [], 'foo_bar_baz');
        $this->assertSession()->statusCodeEquals(200);
    }
}
?>
-----
<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
        // You must call `$this->drupalGet("admin/config/system/actions", []);" before submitForm
        $this->submitForm($edit, 'Create', 'foo_bar_baz');
        $this->assertSession()->statusCodeEquals(200);
    }
}
?>
