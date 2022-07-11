<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/drupal-8.*');

    $rectorConfig->sets([
        PHPUnitSetList::PHPUNIT_60,
        PHPUnitSetList::PHPUNIT_70
    ]);

    $rectorConfig->bootstrapFiles([
        __DIR__ . '/../drupal-phpunit-bootstrap-file.php'
    ]);
};
