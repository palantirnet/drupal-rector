<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\ReplaceModuleHandlerGetNameRector;
use DrupalRector\Drupal10\Rector\Deprecation\ReplaceRebuildThemeDataRector;
use DrupalRector\Rector\Deprecation\ClassConstantToClassConstantRector;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector;
use DrupalRector\Rector\ValueObject\ClassConstantToClassConstantConfiguration;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3407994
    // RendererInterface::renderPlain() deprecated in drupal:10.3.0, removed in drupal:12.0.0.
    // Replaced by RendererInterface::renderInIsolation().
    $rectorConfig->ruleWithConfiguration(MethodToMethodWithCheckRector::class, [
        new MethodToMethodWithCheckConfiguration('Drupal\Core\Render\RendererInterface', 'renderPlain', 'renderInIsolation', '10.3.0'),
    ]);

    // https://www.drupal.org/node/3411269 file_icon_class, file_icon_map
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('10.3.0', 'file_icon_class', 'Drupal\file\IconMimeTypes', 'getIconClass'),
        new FunctionToStaticConfiguration('10.3.0', 'file_icon_map', 'Drupal\file\IconMimeTypes', 'getGenericMimeType'),
    ]);

    // https://www.drupal.org/node/3413196
    // ThemeHandlerInterface::rebuildThemeData() deprecated in drupal:10.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal::service('extension.list.theme')->reset()->getList().
    $rectorConfig->ruleWithConfiguration(ReplaceRebuildThemeDataRector::class, [
        new DrupalIntroducedVersionConfiguration('10.3.0'),
    ]);

    // https://www.drupal.org/node/3310017
    // ModuleHandlerInterface::getName() deprecated in drupal:10.3.0, removed in drupal:12.0.0.
    $rectorConfig->ruleWithConfiguration(ReplaceModuleHandlerGetNameRector::class, [
        new DrupalIntroducedVersionConfiguration('10.3.0'),
    ]);

    // https://www.drupal.org/node/3426517
    // FileSystemInterface::EXISTS_* deprecated in drupal:10.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\Core\File\FileExists enum cases.
    $rectorConfig->ruleWithConfiguration(ClassConstantToClassConstantRector::class, [
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_RENAME',
            'Drupal\Core\File\FileExists',
            'Rename',
            '10.3.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_REPLACE',
            'Drupal\Core\File\FileExists',
            'Replace',
            '10.3.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_ERROR',
            'Drupal\Core\File\FileExists',
            'Error',
            '10.3.0',
        ),
    ]);
};
