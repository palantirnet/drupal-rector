<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FileBuildUriRector;
use DrupalRector\Rector\Deprecation\FileUrlGenerator;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Rector\ValueObject\ExtensionPathConfiguration;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    // Change record: https://www.drupal.org/node/2940438.
    $rectorConfig->ruleWithConfiguration(\DrupalRector\Rector\Deprecation\ExtensionPathRector::class, [
        new ExtensionPathConfiguration('drupal_get_filename', 'getPathname'),
        new ExtensionPathConfiguration('drupal_get_path', 'getPath'),
    ]);

    // Change record: https://www.drupal.org/node/2940031
    $rectorConfig->rule(FileUrlGenerator\FileCreateUrlRector::class);
    $rectorConfig->rule(FileUrlGenerator\FileUrlTransformRelativeRector::class);
    $rectorConfig->rule(FileUrlGenerator\FromUriRector::class);

    // Change record: https://www.drupal.org/node/3223520
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('file_copy', 'file.repository', 'copy'),
        new FunctionToServiceConfiguration('file_move', 'file.repository', 'move'),
        new FunctionToServiceConfiguration('file_save_data', 'file.repository', 'writeData'),
        // Change record: https://www.drupal.org/node/2939099
        new FunctionToServiceConfiguration('render', 'renderer', 'render'),
    ]);

    // Change record: https://www.drupal.org/node/3223091.
    $rectorConfig->rule(FileBuildUriRector::class);
};
