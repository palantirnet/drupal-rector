<?php

use Drupal\Core\ImageToolkit\ImageToolkitInterface;
use Psr\Log\LoggerInterface;

// Constructor shape matches ImageToolkitOperationBase exactly, but the class
// does NOT extend it. The rector must leave it alone — the deprecated
// $toolkit argument is specific to ImageToolkitOperationBase.
class SomeOtherBase
{
    public function __construct(array $configuration, $plugin_id, array $plugin_definition, ImageToolkitInterface $toolkit, LoggerInterface $logger)
    {
    }
}

class LookalikeOperation extends SomeOtherBase
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
use Psr\Log\LoggerInterface;

// Constructor shape matches ImageToolkitOperationBase exactly, but the class
// does NOT extend it. The rector must leave it alone — the deprecated
// $toolkit argument is specific to ImageToolkitOperationBase.
class SomeOtherBase
{
    public function __construct(array $configuration, $plugin_id, array $plugin_definition, ImageToolkitInterface $toolkit, LoggerInterface $logger)
    {
    }
}

class LookalikeOperation extends SomeOtherBase
{
    public function __construct(array $configuration, $plugin_id, array $plugin_definition, ImageToolkitInterface $toolkit, LoggerInterface $logger)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $toolkit, $logger);
    }
}

?>
