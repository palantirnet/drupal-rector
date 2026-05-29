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
        $this->expectDeprecation();
        $this->expectDeprecationMessage('Foo::bar is deprecated');
        $this->expectDeprecationMessageMatches('/deprecated/');
    }
}
?>
