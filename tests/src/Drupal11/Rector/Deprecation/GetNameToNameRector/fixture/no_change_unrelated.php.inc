<?php

class SomeService
{
    public function getName(): string
    {
        return 'example';
    }

    public function doSomething(): void
    {
        $name = $this->getName();
    }
}
?>
-----
<?php

class SomeService
{
    public function getName(): string
    {
        return 'example';
    }

    public function doSomething(): void
    {
        $name = $this->getName();
    }
}
?>
