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
        return \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.0.0', fn() => $this->migrationPluginManager, fn() => $this->getMigrationPluginManager())->createInstances($requirements);
    }
}

?>
