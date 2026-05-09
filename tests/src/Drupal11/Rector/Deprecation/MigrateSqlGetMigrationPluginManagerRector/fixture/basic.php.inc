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
        $manager = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.0.0', fn() => $this->migrationPluginManager, fn() => $this->getMigrationPluginManager());
        $other = $this->someOtherMethod();
    }
}

?>
