<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(FunctionToStaticRector::class, $rectorConfig, false, [
        new FunctionToStaticConfiguration('8.1.0', 'file_directory_os_temp', 'Drupal\Component\FileSystem\FileSystem', 'getOsTemporaryDirectory'),
        new FunctionToStaticConfiguration('10.1.0', 'drupal_rewrite_settings', 'Drupal\Core\Site\SettingsEditor', 'rewrite', [0 => 1, 1 => 0]),
        new FunctionToStaticConfiguration('10.2.0', 'format_size', 'Drupal\Core\StringTranslation\ByteSizeMarkup', 'create'),
        new FunctionToStaticConfiguration('10.3.0', 'file_icon_class', 'Drupal\file\IconMimeTypes', 'getIconClass'),
        new FunctionToStaticConfiguration('10.3.0', 'file_icon_map', 'Drupal\file\IconMimeTypes', 'getGenericMimeType'),
    ]);
};
