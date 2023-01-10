<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FileBuildUriRector;
use DrupalRector\Rector\Deprecation\FileCopyRector;
use DrupalRector\Rector\Deprecation\FileMoveRector;
use DrupalRector\Rector\Deprecation\FileSaveDataRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(FileSaveDataRector::class);
    $services->set(FileMoveRector::class);
    $services->set(FileCopyRector::class);
    $services->set(FileBuildUriRector::class);
};
