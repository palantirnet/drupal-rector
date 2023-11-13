<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\DrupalGetFilenameRector;
use DrupalRector\Rector\Deprecation\DrupalGetPathRector;
use DrupalRector\Drupal9\Rector\Deprecation\FileBuildUriRector;
use DrupalRector\Rector\Deprecation\FileUrlGenerator;
use DrupalRector\Rector\Deprecation\RenderRector;
use DrupalRector\Rector\Deprecation\FileCopyRector;
use DrupalRector\Rector\Deprecation\FileMoveRector;
use DrupalRector\Rector\Deprecation\FileSaveDataRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    // Change record https://www.drupal.org/node/3220952
    $rectorConfig->rule(\DrupalRector\Drupal9\Rector\Deprecation\ModuleLoadInstallRector::class);
};
