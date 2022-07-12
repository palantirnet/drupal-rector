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

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    // Change record: https://www.drupal.org/node/2940438.
    $rectorConfig->ruleWithConfiguration(DrupalGetPathRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
    $rectorConfig->ruleWithConfiguration(DrupalGetFilenameRector::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);

    // Change record: https://www.drupal.org/node/2939099.
    $rectorConfig->rule(RenderRector::class);


    // Change record: https://www.drupal.org/node/2940031
    $rectorConfig->rule(FileUrlGenerator\FileCreateUrlRector::class);
    $rectorConfig->rule(FileUrlGenerator\FileUrlTransformRelativeRector::class);
    $rectorConfig->rule(FileUrlGenerator\FromUriRector::class);

    // Change record: https://www.drupal.org/node/3223520.
    $rectorConfig->rule(FileSaveDataRector::class);
    $rectorConfig->rule(FileMoveRector::class);
    $rectorConfig->rule(FileCopyRector::class);

    // Change record: https://www.drupal.org/node/3223091.
    $rectorConfig->rule(FileBuildUriRector::class);
};
