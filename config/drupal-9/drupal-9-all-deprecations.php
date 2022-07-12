<?php

declare(strict_types=1);

use DrupalRector\Set\Drupal9SetList;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        Drupal9SetList::DRUPAL_90,
        Drupal9SetList::DRUPAL_91,
        Drupal9SetList::DRUPAL_92,
        Drupal9SetList::DRUPAL_93,
    ]);

    $rectorConfig->bootstrapFiles([
        __DIR__ . '/../drupal-phpunit-bootstrap-file.php'
    ]);
};
