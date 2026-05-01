<?php

class MyIdMap
{
    public function doSomething(array $requirements): array
    {
        return $this->getMigrationPluginManager()->createInstances($requirements);
    }
}

?>
-----
<?php

class MyIdMap
{
    public function doSomething(array $requirements): array
    {
        return $this->migrationPluginManager->createInstances($requirements);
    }
}

?>
