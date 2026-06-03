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
        \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $this->expectUserDeprecationMessage('Drupal trait variant with a string arg'), fn() => $this->expectDeprecation('Drupal trait variant with a string arg'));
        \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $this->expectUserDeprecationMessage('foo() is deprecated in mymodule:2.0.0 and is removed from mymodule:3.0.0.'), fn() => $this->expectDeprecation('foo() is deprecated in mymodule:2.0.0 and is removed from mymodule:3.0.0.'));
    }
}
?>
