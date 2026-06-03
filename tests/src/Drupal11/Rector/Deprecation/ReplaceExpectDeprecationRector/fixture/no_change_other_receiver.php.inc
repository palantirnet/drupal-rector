<?php

class ExampleTest {
    public function testThing($helper) {
        $helper->expectDeprecation();
        $helper->expectDeprecationMessage('foo');
    }
}
?>
-----
<?php

class ExampleTest {
    public function testThing($helper) {
        $helper->expectDeprecation();
        $helper->expectDeprecationMessage('foo');
    }
}
?>
