<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        $this->drupalPostForm('admin/config/system/actions', $edit, 'Create', ['foo' => 'bar']);
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
        $this->drupalGet('admin/config/system/actions', ['foo' => 'bar']);
        $this->submitForm($edit, 'Create');
        $this->assertSession()->statusCodeEquals(200);
    }
}
?>
