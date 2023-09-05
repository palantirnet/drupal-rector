<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/2802569
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new \DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration(
            'file_directory_os_temp',
            'Drupal\Component\FileSystem\FileSystem',
            'getOsTemporaryDirectory'
        ),
    ]);
};
