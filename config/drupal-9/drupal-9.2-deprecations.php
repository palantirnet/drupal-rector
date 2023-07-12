<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ClearCsrfTokenSeed;
use Rector\PHPUnit\Set\PHPUnitSetList;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        PHPUnitSetList::PHPUNIT_100
    ]);
    // Change record: https://www.drupal.org/node/3187914
    $rectorConfig->ruleWithConfiguration(ClearCsrfTokenSeed::class, [
            'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
        ]);
};
