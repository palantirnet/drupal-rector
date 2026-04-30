<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ClassConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\ClassConstantToClassConstantConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3411269 file_icon_class, file_icon_map
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('10.3.0', 'file_icon_class', 'Drupal\file\IconMimeTypes', 'getIconClass'),
        new FunctionToStaticConfiguration('10.3.0', 'file_icon_map', 'Drupal\file\IconMimeTypes', 'getGenericMimeType'),
    ]);

    // https://www.drupal.org/node/3575575
    // FileSystemInterface::EXISTS_* deprecated in drupal:10.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\Core\File\FileExists enum cases.
    $rectorConfig->ruleWithConfiguration(ClassConstantToClassConstantRector::class, [
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_RENAME',
            'Drupal\Core\File\FileExists',
            'Rename',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_REPLACE',
            'Drupal\Core\File\FileExists',
            'Replace',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_ERROR',
            'Drupal\Core\File\FileExists',
            'Error',
        ),
    ]);
};
