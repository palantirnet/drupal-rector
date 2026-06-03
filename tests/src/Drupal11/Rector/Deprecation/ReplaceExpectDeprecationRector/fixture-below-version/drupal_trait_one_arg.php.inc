<?php

class ExampleTest {
    public function testThing() {
        $this->expectDeprecation('Drupal trait variant with a string arg');
        $this->expectDeprecation('foo() is deprecated in mymodule:2.0.0 and is removed from mymodule:3.0.0.');
    }
}
?>
-----
<?php

class ExampleTest {
    public function testThing() {
        $this->expectDeprecation('Drupal trait variant with a string arg');
        $this->expectDeprecation('foo() is deprecated in mymodule:2.0.0 and is removed from mymodule:3.0.0.');
    }
}
?>
