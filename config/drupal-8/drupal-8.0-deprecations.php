<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\DBDeleteRector;
use DrupalRector\Rector\Deprecation\DBInsertRector;
use DrupalRector\Rector\Deprecation\DBQueryRector;
use DrupalRector\Rector\Deprecation\DBSelectRector;
use DrupalRector\Rector\Deprecation\DBUpdateRector;
use DrupalRector\Rector\Deprecation\DrupalLRector;
use DrupalRector\Rector\Deprecation\DrupalURLRector;
use DrupalRector\Rector\Deprecation\EntityCreateRector;
use DrupalRector\Rector\Deprecation\EntityDeleteMultipleRector;
use DrupalRector\Rector\Deprecation\EntityInterfaceLinkRector;
use DrupalRector\Rector\Deprecation\EntityInterfaceUrlInfoRector;
use DrupalRector\Rector\Deprecation\EntityLoadRector;
use DrupalRector\Rector\Deprecation\EntityManagerRector;
use DrupalRector\Rector\Deprecation\EntityViewRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\Deprecation\LinkGeneratorTraitLRector;
use DrupalRector\Rector\Deprecation\SafeMarkupFormatRector;
use DrupalRector\Rector\ValueObject\EntityLoadConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(DBInsertRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
    ]);
    $rectorConfig->ruleWithConfiguration(DBSelectRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
    ]);
    $rectorConfig->ruleWithConfiguration(DBQueryRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
    ]);
    $rectorConfig->ruleWithConfiguration(DBDeleteRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
    ]);
    $rectorConfig->ruleWithConfiguration(DBUpdateRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
    ]);

    $rectorConfig->rule(DrupalURLRector::class);
    $rectorConfig->rule(DrupalLRector::class);
    $rectorConfig->rule(EntityCreateRector::class);
    $rectorConfig->rule(EntityDeleteMultipleRector::class);


    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        // https://www.drupal.org/node/2418133
        new FunctionToServiceConfiguration('drupal_realpath', 'file_system', 'realpath'),
        // https://www.drupal.org/node/2912696
        new FunctionToServiceConfiguration('drupal_render', 'renderer', 'render'),
        // https://www.drupal.org/node/2912696
        new FunctionToServiceConfiguration('drupal_render_root', 'renderer', 'renderRoot'),
        // https://www.drupal.org/node/1876852
        new FunctionToServiceConfiguration('format_date', 'date.formatter', 'format'),

    ]);

    $rectorConfig->ruleWithConfiguration(EntityInterfaceLinkRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $rectorConfig->ruleWithConfiguration(EntityInterfaceUrlInfoRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $rectorConfig->ruleWithConfiguration(EntityLoadRector::class, [
        new EntityLoadConfiguration('entity'),
        new EntityLoadConfiguration('file'),
        new EntityLoadConfiguration('node'),
        new EntityLoadConfiguration('user'),
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
    ]);

    $rectorConfig->rule(EntityViewRector::class);

    $rectorConfig->ruleWithConfiguration(EntityManagerRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $rectorConfig->ruleWithConfiguration(LinkGeneratorTraitLRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $rectorConfig->rule(SafeMarkupFormatRector::class);
};
