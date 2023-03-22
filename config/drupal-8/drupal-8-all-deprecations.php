<?php

declare(strict_types=1);

use DrupalRector\Set\Drupal8SetList;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Rector\ClassMethod\AddDoesNotPerformAssertionToNonAssertingTestRector;
use Rector\PHPUnit\Set\PHPUnitSetList;

return static function (RectorConfig $rectorConfig): void {

    $rectorConfig->sets([
        PHPUnitSetList::PHPUNIT_60,
        PHPUnitSetList::PHPUNIT_70,
        Drupal8SetList::DRUPAL_80,
        Drupal8SetList::DRUPAL_81,
        Drupal8SetList::DRUPAL_82,
        Drupal8SetList::DRUPAL_83,
        Drupal8SetList::DRUPAL_84,
        Drupal8SetList::DRUPAL_85,
        Drupal8SetList::DRUPAL_86,
        Drupal8SetList::DRUPAL_87,
        Drupal8SetList::DRUPAL_88,
    ]);

    // Drupal performs assertions in the ::setUp methods of its test suites,
    // the following rule adds unneeded annotations.
    $rectorConfig->services()->remove(AddDoesNotPerformAssertionToNonAssertingTestRector::class);

    $rectorConfig->bootstrapFiles([
        __DIR__ . '/../drupal-phpunit-bootstrap-file.php'
    ]);
};
