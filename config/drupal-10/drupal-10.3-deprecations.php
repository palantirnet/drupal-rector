<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3411269 file_icon_class, file_icon_map
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('10.3.0', 'file_icon_class', 'Drupal\file\IconMimeTypes', 'getIconClass'),
        new FunctionToStaticConfiguration('10.3.0', 'file_icon_map', 'Drupal\file\IconMimeTypes', 'getGenericMimeType'),
    ]);
};
