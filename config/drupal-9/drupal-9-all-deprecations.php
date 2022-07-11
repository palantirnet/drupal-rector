<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/drupal-9.*');

    $rectorConfig->bootstrapFiles([
        __DIR__ . '/../drupal-phpunit-bootstrap-file.php'
    ]);
};
