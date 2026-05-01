<?php

use Drupal\migrate\Plugin\migrate\id_map\Sql;

class MyIdMap extends Sql
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

use Drupal\migrate\Plugin\migrate\id_map\Sql;

class MyIdMap extends Sql
{
    public function doSomething(): void
    {
        $manager = $this->migrationPluginManager;
        $other = $this->someOtherMethod();
    }
}

?>
