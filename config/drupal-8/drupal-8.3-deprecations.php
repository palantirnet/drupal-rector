<?php

declare(strict_types=1);

use DrupalRector\Drupal8\Rector\Deprecation\RequestTimeConstRector;
use DrupalRector\Services\AddCommentService;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function () {
        return new AddCommentService();
    });
    $rectorConfig->rule(RequestTimeConstRector::class);
};
