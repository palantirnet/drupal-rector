<?php

use Drupal\Core\ImageToolkit\ImageToolkitInterface;
use Drupal\Core\ImageToolkit\ImageToolkitOperationBase;
use Psr\Log\LoggerInterface;

class MyOperationWithBodyUse extends ImageToolkitOperationBase
{
    public function __construct(array $configuration, $plugin_id, array $plugin_definition, ImageToolkitInterface $toolkit, LoggerInterface $logger)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $toolkit, $logger);
        $this->customToolkit = $toolkit;
    }
}

?>
-----
<?php

use Drupal\Core\ImageToolkit\ImageToolkitInterface;
use Drupal\Core\ImageToolkit\ImageToolkitOperationBase;
use Psr\Log\LoggerInterface;

class MyOperationWithBodyUse extends ImageToolkitOperationBase
{
    public function __construct(array $configuration, $plugin_id, array $plugin_definition, ImageToolkitInterface $toolkit, LoggerInterface $logger)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $toolkit, $logger);
        $this->customToolkit = $toolkit;
    }
}

?>
