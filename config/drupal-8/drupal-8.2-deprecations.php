<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FileDirectoryOsTempRector;
use DrupalRector\Services\AddCommentService;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function() {
        return new AddCommentService();
    });
    $rectorConfig->rule(FileDirectoryOsTempRector::class);
};
