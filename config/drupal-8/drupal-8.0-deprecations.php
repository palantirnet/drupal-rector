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
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(DBInsertRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $services->set(DBSelectRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $services->set(DBQueryRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $services->set(DBDeleteRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $services->set(DBUpdateRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $services->set(DrupalRenderRector::class);

    $services->set(DrupalRenderRootRector::class);

    $services->set(DrupalURLRector::class);

    $services->set(DrupalLRector::class);

    $services->set(DrupalRealpathRector::class);

    $services->set(EntityCreateRector::class);

    $services->set(EntityDeleteMultipleRector::class);

    $services->set(EntityInterfaceLinkRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $services->set(EntityInterfaceUrlInfoRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $services->set(EntityLoadRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $services->set(EntityViewRector::class);

    $services->set(EntityManagerRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $services->set(FormatDateRector::class);

    $services->set(FileLoadRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $services->set(LinkGeneratorTraitLRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $services->set(NodeLoadRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    $services->set(SafeMarkupFormatRector::class);

    $services->set(UserLoadRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
};
