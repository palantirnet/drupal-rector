<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\DBDeleteRector;
use DrupalRector\Rector\Deprecation\DBInsertRector;
use DrupalRector\Rector\Deprecation\DBQueryRector;
use DrupalRector\Rector\Deprecation\DBSelectRector;
use DrupalRector\Rector\Deprecation\DBUpdateRector;
use DrupalRector\Rector\Deprecation\DrupalLRector;
use DrupalRector\Rector\Deprecation\DrupalRealpathRector;
use DrupalRector\Rector\Deprecation\DrupalRenderRector;
use DrupalRector\Rector\Deprecation\DrupalRenderRootRector;
use DrupalRector\Rector\Deprecation\DrupalURLRector;
use DrupalRector\Rector\Deprecation\EntityCreateRector;
use DrupalRector\Rector\Deprecation\EntityDeleteMultipleRector;
use DrupalRector\Rector\Deprecation\EntityInterfaceLinkRector;
use DrupalRector\Rector\Deprecation\EntityInterfaceUrlInfoRector;
use DrupalRector\Rector\Deprecation\EntityLoadRector;
use DrupalRector\Rector\Deprecation\EntityManagerRector;
use DrupalRector\Rector\Deprecation\EntityViewRector;
use DrupalRector\Rector\Deprecation\FileLoadRector;
use DrupalRector\Rector\Deprecation\FormatDateRector;
use DrupalRector\Rector\Deprecation\LinkGeneratorTraitLRector;
use DrupalRector\Rector\Deprecation\NodeLoadRector;
use DrupalRector\Rector\Deprecation\SafeMarkupFormatRector;
use DrupalRector\Rector\Deprecation\UserLoadRector;
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

    $rectorConfig->rule(DrupalRenderRector::class);
    $rectorConfig->rule(DrupalRenderRootRector::class);
    $rectorConfig->rule(DrupalURLRector::class);
    $rectorConfig->rule(DrupalLRector::class);
    $rectorConfig->rule(DrupalRealpathRector::class);
    $rectorConfig->rule(EntityCreateRector::class);
    $rectorConfig->rule(EntityDeleteMultipleRector::class);

    $rectorConfig->ruleWithConfiguration(EntityInterfaceLinkRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $rectorConfig->ruleWithConfiguration(EntityInterfaceUrlInfoRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $rectorConfig->ruleWithConfiguration(EntityLoadRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $rectorConfig->rule(EntityViewRector::class);

    $rectorConfig->ruleWithConfiguration(EntityManagerRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $rectorConfig->rule(FormatDateRector::class);

    $rectorConfig->ruleWithConfiguration(FileLoadRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $rectorConfig->ruleWithConfiguration(LinkGeneratorTraitLRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $rectorConfig->ruleWithConfiguration(NodeLoadRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $rectorConfig->rule(SafeMarkupFormatRector::class);

    $rectorConfig->ruleWithConfiguration(UserLoadRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
};
