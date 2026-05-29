<?php

use Drupal\Core\ImageToolkit\ImageToolkitInterface;
use Drupal\Core\ImageToolkit\ImageToolkitOperationBase;
use Psr\Log\LoggerInterface;

class MyOperation extends ImageToolkitOperationBase
{
    public function __construct(array $configuration, $plugin_id, array $plugin_definition, ImageToolkitInterface $toolkit, LoggerInterface $logger)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $toolkit, $logger);
    }
}

?>
-----
<?php

use Drupal\Core\ImageToolkit\ImageToolkitInterface;
use Drupal\Core\ImageToolkit\ImageToolkitOperationBase;
use Psr\Log\LoggerInterface;

class MyOperation extends ImageToolkitOperationBase
{
    public function __construct(array $configuration, $plugin_id, array $plugin_definition, LoggerInterface $logger)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $logger);
    }
}

?>
