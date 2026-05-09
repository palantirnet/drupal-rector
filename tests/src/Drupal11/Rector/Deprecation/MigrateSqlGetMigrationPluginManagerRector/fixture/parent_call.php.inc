<?php

use Drupal\migrate\Plugin\migrate\id_map\Sql;

// A subclass overriding getMigrationPluginManager() that delegates to parent.
class MyIdMap extends Sql
{
    public function getMigrationPluginManager()
    {
        return parent::getMigrationPluginManager();
    }
}

?>
-----
<?php

use Drupal\migrate\Plugin\migrate\id_map\Sql;

// A subclass overriding getMigrationPluginManager() that delegates to parent.
class MyIdMap extends Sql
{
    public function getMigrationPluginManager()
    {
        return \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.0.0', fn() => $this->migrationPluginManager, fn() => parent::getMigrationPluginManager());
    }
}

?>
