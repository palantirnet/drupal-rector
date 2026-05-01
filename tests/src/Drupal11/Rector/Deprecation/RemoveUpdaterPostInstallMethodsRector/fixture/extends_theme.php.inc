<?php

use Drupal\Core\Updater\Theme;

class MyThemeUpdater extends Theme
{
    public function postInstallTasks()
    {
        return [['#type' => 'link', '#title' => 'Theme task']];
    }

    public function keepThisMethod()
    {
        return 'kept';
    }
}

?>
-----
<?php

use Drupal\Core\Updater\Theme;

class MyThemeUpdater extends Theme
{
    public function keepThisMethod()
    {
        return 'kept';
    }
}

?>
