<?php

use Psr\Log\LoggerInterface;

class MyUnrelatedClass
{
    public function __construct(array $configuration, $plugin_id, array $plugin_definition, string $unrelated, LoggerInterface $logger)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $unrelated, $logger);
    }
}

?>
-----
<?php

use Psr\Log\LoggerInterface;

class MyUnrelatedClass
{
    public function __construct(array $configuration, $plugin_id, array $plugin_definition, string $unrelated, LoggerInterface $logger)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $unrelated, $logger);
    }
}

?>
