<?php

declare(strict_types=1);

use DrupalRector\Set\Drupal11SetList;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        Drupal11SetList::DRUPAL_110,
        Drupal11SetList::DRUPAL_111,
        Drupal11SetList::DRUPAL_112,
        Drupal11SetList::DRUPAL_113,
        Drupal11SetList::DRUPAL_114,
    ]);

    $rectorConfig->bootstrapFiles([
        __DIR__.'/../drupal-phpunit-bootstrap-file.php',
    ]);
};
