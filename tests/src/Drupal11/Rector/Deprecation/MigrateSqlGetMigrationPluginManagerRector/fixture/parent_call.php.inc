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
        return $this->migrationPluginManager;
    }
}

?>
