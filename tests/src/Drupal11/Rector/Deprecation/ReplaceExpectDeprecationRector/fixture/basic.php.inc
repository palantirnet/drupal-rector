<?php

class ExampleTest {
    public function testThing() {
        $this->expectDeprecation();
        $this->expectDeprecationMessage('Foo::bar is deprecated');
        $this->expectDeprecationMessageMatches('/deprecated/');
    }
}
?>
-----
<?php

class ExampleTest {
    public function testThing() {
        \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $this->expectUserDeprecationMessage('Foo::bar is deprecated'), fn() => $this->expectDeprecationMessage('Foo::bar is deprecated'));
        \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $this->expectUserDeprecationMessageMatches('/deprecated/'), fn() => $this->expectDeprecationMessageMatches('/deprecated/'));
    }
}
?>
