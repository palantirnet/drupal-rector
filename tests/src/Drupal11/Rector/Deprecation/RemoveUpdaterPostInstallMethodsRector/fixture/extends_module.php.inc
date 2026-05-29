<?php

use Drupal\Core\Updater\Module;

class MyModuleUpdater extends Module
{
    public function postInstall()
    {
        \Drupal::logger('mymodule')->info('Module installed.');
    }

    public function keepThisMethod()
    {
        return 'kept';
    }
}

?>
-----
<?php

use Drupal\Core\Updater\Module;

class MyModuleUpdater extends Module
{
    public function keepThisMethod()
    {
        return 'kept';
    }
}

?>
