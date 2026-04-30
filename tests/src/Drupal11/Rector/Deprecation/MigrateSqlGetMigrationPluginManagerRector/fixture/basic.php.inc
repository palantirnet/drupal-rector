<?php

class MyIdMap
{
    public function doSomething(): void
    {
        $manager = $this->getMigrationPluginManager();
        $other = $this->someOtherMethod();
    }
}

?>
-----
<?php

class MyIdMap
{
    public function doSomething(): void
    {
        $manager = $this->migrationPluginManager;
        $other = $this->someOtherMethod();
    }
}

?>
