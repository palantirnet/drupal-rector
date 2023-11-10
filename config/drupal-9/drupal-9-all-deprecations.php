<?php

declare(strict_types=1);

use DrupalRector\Set\Drupal9SetList;
use DrupalRector\Services\AddCommentService;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function() {
        return new AddCommentService();
    });
    $rectorConfig->sets([
        Drupal9SetList::DRUPAL_90,
        Drupal9SetList::DRUPAL_91,
        Drupal9SetList::DRUPAL_92,
        Drupal9SetList::DRUPAL_93,
        Drupal9SetList::DRUPAL_94,
        PHPUnitLevelSetList::UP_TO_PHPUNIT_90,
    ]);

    $rectorConfig->bootstrapFiles([
        __DIR__ . '/../drupal-phpunit-bootstrap-file.php'
    ]);
};
