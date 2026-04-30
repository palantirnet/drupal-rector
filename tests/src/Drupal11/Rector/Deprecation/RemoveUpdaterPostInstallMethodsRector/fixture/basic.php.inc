<?php

use Drupal\Core\Updater\Updater;

class MyUpdater extends Updater
{
    public function postInstallTasks()
    {
        return [['#type' => 'link', '#title' => 'My task']];
    }

    public function postInstall()
    {
        \Drupal::logger('mymodule')->info('Installed.');
    }

    public function keepThisMethod()
    {
        return 'kept';
    }
}

class UnrelatedClass
{
    public function postInstall() {}
}

?>
-----
<?php

use Drupal\Core\Updater\Updater;

class MyUpdater extends Updater
{
    public function keepThisMethod()
    {
        return 'kept';
    }
}

class UnrelatedClass
{
    public function postInstall() {}
}

?>
