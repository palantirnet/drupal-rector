<?php

declare(strict_types=1);

use DrupalRector\Set\Drupal12SetList;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        Drupal12SetList::DRUPAL_120,
    ]);

    $rectorConfig->bootstrapFiles([
        __DIR__.'/../drupal-phpunit-bootstrap-file.php',
    ]);
};
