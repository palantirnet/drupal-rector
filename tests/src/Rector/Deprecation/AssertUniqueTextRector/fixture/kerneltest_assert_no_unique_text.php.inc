<?php

class KernelTestExampleTest extends \Drupal\KernelTests\KernelTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $this->assertNoUniqueText('Duplicated message');
    }
}
?>
-----
<?php

class KernelTestExampleTest extends \Drupal\KernelTests\KernelTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $this->assertNoUniqueText('Duplicated message');
    }
}
?>
