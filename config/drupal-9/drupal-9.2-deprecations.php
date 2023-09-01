<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ClearCsrfTokenSeed;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        PHPUnitSetList::PHPUNIT_90
    ]);
    // Change record: https://www.drupal.org/node/3187914
    $rectorConfig->rule(ClearCsrfTokenSeed::class);
};
