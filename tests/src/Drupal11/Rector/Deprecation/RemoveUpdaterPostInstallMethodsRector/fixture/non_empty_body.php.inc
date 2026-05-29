<?php

use Drupal\Core\Updater\Updater;

class MyUpdaterWithBody extends Updater
{
    public function postInstall()
    {
        \Drupal::logger('mymodule')->info('Step 1.');
        \Drupal::logger('mymodule')->info('Step 2.');
        $this->doSomething();
    }

    public function postInstallTasks()
    {
        $tasks = [];
        $tasks[] = ['#type' => 'link', '#title' => 'Task 1'];
        $tasks[] = ['#type' => 'link', '#title' => 'Task 2'];
        return $tasks;
    }
}

?>
-----
<?php

use Drupal\Core\Updater\Updater;

class MyUpdaterWithBody extends Updater
{
}

?>
