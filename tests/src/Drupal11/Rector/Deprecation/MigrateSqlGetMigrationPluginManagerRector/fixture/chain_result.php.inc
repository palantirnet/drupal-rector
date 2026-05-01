<?php

use Drupal\migrate\Plugin\migrate\id_map\Sql;

class MyIdMap extends Sql
{
    public function doSomething(array $requirements): array
    {
        return $this->getMigrationPluginManager()->createInstances($requirements);
    }
}

?>
-----
<?php

use Drupal\migrate\Plugin\migrate\id_map\Sql;

class MyIdMap extends Sql
{
    public function doSomething(array $requirements): array
    {
        return $this->migrationPluginManager->createInstances($requirements);
    }
}

?>
