<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\EntityGetDisplayRector;
use DrupalRector\Rector\Deprecation\EntityGetFormDisplayRector;
use DrupalRector\Rector\Deprecation\EntityTypeGetLowercaseLabelRector;
use DrupalRector\Rector\Deprecation\FileDefaultSchemeRector;
use DrupalRector\Rector\Deprecation\FileDirectoryTempRector;
use DrupalRector\Rector\Deprecation\FileScanDirectoryRector;
use DrupalRector\Rector\Deprecation\FileUriTargetRector;
use DrupalRector\Rector\Deprecation\PathAliasManagerServiceNameRector;
use DrupalRector\Rector\Deprecation\PathAliasRepositoryRector;
use DrupalRector\Rector\Deprecation\PathAliasWhitelistServiceNameRector;
use DrupalRector\Rector\Deprecation\PathProcessorAliasServiceNameRector;
use DrupalRector\Rector\Deprecation\PathSubscriberServiceNameRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(PathAliasManagerServiceNameRector::class);

    $services->set(PathAliasWhitelistServiceNameRector::class);

    $services->set(PathSubscriberServiceNameRector::class);

    $services->set(PathProcessorAliasServiceNameRector::class);

    $services->set(PathAliasRepositoryRector::class);

    $services->set(FileDefaultSchemeRector::class);

    $services->set(EntityGetDisplayRector::class);

    $services->set(EntityGetFormDisplayRector::class);

    $services->set(EntityTypeGetLowercaseLabelRector::class);

    $services->set(FileScanDirectoryRector::class);

    $services->set(FileDirectoryTempRector::class);

    $services->set(FileUriTargetRector::class);
};
