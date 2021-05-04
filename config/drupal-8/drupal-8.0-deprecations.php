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

    $services->set(DBInsertRector::class);

    $services->set(DBSelectRector::class);

    $services->set(DBQueryRector::class);

    $services->set(DBDeleteRector::class);

    $services->set(DBUpdateRector::class);

    $services->set(DrupalRenderRector::class);

    $services->set(DrupalRenderRootRector::class);

    $services->set(DrupalURLRector::class);

    $services->set(DrupalLRector::class);

    $services->set(DrupalRealpathRector::class);

    $services->set(EntityCreateRector::class);

    $services->set(EntityDeleteMultipleRector::class);

    $services->set(EntityInterfaceLinkRector::class);

    $services->set(EntityInterfaceUrlInfoRector::class);

    $services->set(EntityLoadRector::class);

    $services->set(EntityViewRector::class);

    $services->set(EntityManagerRector::class);

    $services->set(FormatDateRector::class);

    $services->set(FileLoadRector::class);

    $services->set(LinkGeneratorTraitLRector::class);

    $services->set(NodeLoadRector::class);

    $services->set(SafeMarkupFormatRector::class);

    $services->set(UserLoadRector::class);
};
