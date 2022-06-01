<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\DrupalGetFilenameRector;
use DrupalRector\Rector\Deprecation\DrupalGetPathRector;
use DrupalRector\Rector\Deprecation\FileBuildUriRector;
use DrupalRector\Rector\Deprecation\FileUrlGenerator;
use DrupalRector\Rector\Deprecation\RenderRector;
use DrupalRector\Rector\Deprecation\FileCopyRector;
use DrupalRector\Rector\Deprecation\FileMoveRector;
use DrupalRector\Rector\Deprecation\FileSaveDataRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    // Change record: https://www.drupal.org/node/2940438.
    $services->set(DrupalGetPathRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $services->set(DrupalGetFilenameRector::class)
        ->configure([
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    // Change record: https://www.drupal.org/node/2939099.
    $services->set(RenderRector::class);


    // Change record: https://www.drupal.org/node/2940031
    $services->set(FileUrlGenerator\FileCreateUrlRector::class);
    $services->set(FileUrlGenerator\FileUrlTransformRelativeRector::class);
    $services->set(FileUrlGenerator\FromUriRector::class);

    // Change record: https://www.drupal.org/node/3223520.
    $services->set(FileSaveDataRector::class);
    $services->set(FileMoveRector::class);
    $services->set(FileCopyRector::class);

    // Change record: https://www.drupal.org/node/3223091.
    $services->set(FileBuildUriRector::class);
};
