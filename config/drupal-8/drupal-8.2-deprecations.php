<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Services\AddCommentService;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function () {
        return new AddCommentService();
    });
    // https://www.drupal.org/node/2802569
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration(
            '8.2.0',
            'file_directory_os_temp',
            'Drupal\Component\FileSystem\FileSystem',
            'getOsTemporaryDirectory'
        ),
    ]);
};
