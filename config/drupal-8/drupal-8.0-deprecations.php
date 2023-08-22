<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\DBRector;
use DrupalRector\Rector\Deprecation\DrupalLRector;
use DrupalRector\Rector\Deprecation\DrupalURLRector;
use DrupalRector\Rector\Deprecation\EntityCreateRector;
use DrupalRector\Rector\Deprecation\EntityDeleteMultipleRector;
use DrupalRector\Rector\Deprecation\EntityInterfaceLinkRector;
use DrupalRector\Rector\Deprecation\EntityLoadRector;
use DrupalRector\Rector\Deprecation\EntityManagerRector;
use DrupalRector\Rector\Deprecation\EntityViewRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\Deprecation\LinkGeneratorTraitLRector;
use DrupalRector\Rector\Deprecation\SafeMarkupFormatRector;
use DrupalRector\Rector\ValueObject\DBConfiguration;
use DrupalRector\Rector\ValueObject\EntityLoadConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(DBRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        // https://www.drupal.org/node/2993033
        new DBConfiguration('db_delete', 2),
        new DBConfiguration('db_insert', 2),
        new DBConfiguration('db_query', 3),
        new DBConfiguration('db_select', 3),
        new DBConfiguration('db_update', 2),
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

    $rectorConfig->ruleWithConfiguration(\DrupalRector\Rector\Deprecation\Base\MethodToMethodWithCheckRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        // https://www.drupal.org/node/2614344
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Entity\EntityInterface', 'urlInfo', 'toUrl'),
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
