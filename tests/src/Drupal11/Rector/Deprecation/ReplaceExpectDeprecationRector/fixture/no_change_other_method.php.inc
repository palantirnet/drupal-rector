<?php

class ExampleTest {
    public function testThing() {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('boom');
    }
}
?>
-----
<?php

class ExampleTest {
    public function testThing() {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('boom');
    }
}
?>
