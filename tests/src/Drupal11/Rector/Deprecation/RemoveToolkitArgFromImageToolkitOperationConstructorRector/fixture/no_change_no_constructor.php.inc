<?php

use Drupal\Core\ImageToolkit\ImageToolkitOperationBase;

class MyOperationNoCtor extends ImageToolkitOperationBase
{
    protected function arguments()
    {
        return [];
    }

    protected function execute(array $arguments)
    {
        return TRUE;
    }
}

?>
-----
<?php

use Drupal\Core\ImageToolkit\ImageToolkitOperationBase;

class MyOperationNoCtor extends ImageToolkitOperationBase
{
    protected function arguments()
    {
        return [];
    }

    protected function execute(array $arguments)
    {
        return TRUE;
    }
}

?>
