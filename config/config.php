<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    throw new \RuntimeException('Verifying that config was hit ' . __FILE__);
    $rectorConfig->bootstrapFiles([
        __DIR__ . '/drupal-phpunit-bootstrap-file.php'
    ]);
};
