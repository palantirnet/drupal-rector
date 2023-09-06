<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ClearCsrfTokenSeed;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use DrupalRector\Services\AddCommentService;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(AddCommentService::class, function() {
        return new AddCommentService();
    });

    $rectorConfig->sets([
        PHPUnitSetList::PHPUNIT_90
    ]);
    // Change record: https://www.drupal.org/node/3187914
    $rectorConfig->rule(ClearCsrfTokenSeed::class);
};
