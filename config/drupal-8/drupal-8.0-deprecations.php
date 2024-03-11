<?php

declare(strict_types=1);

use DrupalRector\Drupal8\Rector\Deprecation\DBRector;
use DrupalRector\Drupal8\Rector\Deprecation\DrupalLRector;
use DrupalRector\Drupal8\Rector\Deprecation\DrupalURLRector;
use DrupalRector\Drupal8\Rector\Deprecation\EntityCreateRector;
use DrupalRector\Drupal8\Rector\Deprecation\EntityDeleteMultipleRector;
use DrupalRector\Drupal8\Rector\Deprecation\EntityInterfaceLinkRector;
use DrupalRector\Drupal8\Rector\Deprecation\EntityLoadRector;
use DrupalRector\Drupal8\Rector\Deprecation\EntityManagerRector;
use DrupalRector\Drupal8\Rector\Deprecation\EntityViewRector;
use DrupalRector\Drupal8\Rector\Deprecation\LinkGeneratorTraitLRector;
use DrupalRector\Drupal8\Rector\Deprecation\SafeMarkupFormatRector;
use DrupalRector\Drupal8\Rector\ValueObject\DBConfiguration;
use DrupalRector\Drupal8\Rector\ValueObject\EntityLoadConfiguration;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use DrupalRector\Services\AddCommentService;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function () {
        return new AddCommentService();
    });

    $rectorConfig->ruleWithConfiguration(DBRector::class, [
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
        new FunctionToServiceConfiguration('8.0.0', 'drupal_realpath', 'file_system', 'realpath'),
        // https://www.drupal.org/node/2912696
        new FunctionToServiceConfiguration('8.0.0', 'drupal_render', 'renderer', 'render'),
        // https://www.drupal.org/node/2912696
        new FunctionToServiceConfiguration('8.0.0', 'drupal_render_root', 'renderer', 'renderRoot'),
        // https://www.drupal.org/node/1876852
        new FunctionToServiceConfiguration('8.0.0', 'format_date', 'date.formatter', 'format'),
    ]);

    $rectorConfig->rule(EntityInterfaceLinkRector::class);

    $rectorConfig->ruleWithConfiguration(MethodToMethodWithCheckRector::class, [
        // https://www.drupal.org/node/2614344
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Entity\EntityInterface', 'urlInfo', 'toUrl'),
    ]);

    $rectorConfig->ruleWithConfiguration(EntityLoadRector::class, [
        new EntityLoadConfiguration('entity'),
        new EntityLoadConfiguration('file'),
        new EntityLoadConfiguration('node'),
        new EntityLoadConfiguration('user'),
    ]);

    $rectorConfig->rule(EntityViewRector::class);
    $rectorConfig->rule(EntityManagerRector::class);
    $rectorConfig->rule(LinkGeneratorTraitLRector::class);

    $rectorConfig->rule(SafeMarkupFormatRector::class);
};
