<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $edit = [];
        $edit['action'] = 'action_goto_action';
        $options = ['foo' => 'bar'];
        $this->drupalPostForm('admin/config/system/actions', $edit, 'Create', $options);
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
        $options = ['foo' => 'bar'];
        $this->drupalGet('admin/config/system/actions', $options);
        $this->submitForm($edit, 'Create');
        $this->assertSession()->statusCodeEquals(200);
    }
}
?>
