<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FileCreateDirectoryRector;
use DrupalRector\Rector\Deprecation\FileExistsRenameRector;
use DrupalRector\Rector\Deprecation\FileExistsReplaceRector;
use DrupalRector\Rector\Deprecation\FileModifyPermissionsRector;
use DrupalRector\Rector\Deprecation\FilePrepareDirectoryRector;
use DrupalRector\Rector\Deprecation\FileUnmanagedSaveDataRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(FilePrepareDirectoryRector::class);

    $services->set(FileCreateDirectoryRector::class);

    $services->set(FileExistsReplaceRector::class);

    $services->set(FileUnmanagedSaveDataRector::class);

    $services->set(FileModifyPermissionsRector::class);

    $services->set(FileExistsRenameRector::class);
};
