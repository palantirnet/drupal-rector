<?php

class SomeTest extends \PHPUnit\Framework\TestCase
{
    public function testGetName(): void
    {
        $name = $this->getName();
    }

    public function testGetNameWithFalse(): void
    {
        $name = $this->getName(false);
    }
}
?>
-----
<?php

class SomeTest extends \PHPUnit\Framework\TestCase
{
    public function testGetName(): void
    {
        $name = $this->name();
    }

    public function testGetNameWithFalse(): void
    {
        $name = $this->name();
    }
}
?>
