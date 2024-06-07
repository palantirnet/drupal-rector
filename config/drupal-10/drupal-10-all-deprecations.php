<?php

declare(strict_types=1);

use DrupalRector\Set\Drupal10SetList;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        Drupal10SetList::DRUPAL_100,
        Drupal10SetList::DRUPAL_101,
        Drupal10SetList::DRUPAL_102,
        Drupal10SetList::DRUPAL_103,
    ]);

    $rectorConfig->bootstrapFiles([
        __DIR__.'/../drupal-phpunit-bootstrap-file.php',
    ]);
};
