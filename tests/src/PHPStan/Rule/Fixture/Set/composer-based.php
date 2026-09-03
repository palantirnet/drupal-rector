<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\LoadAllIncludesRector;
use DrupalRector\Rector\Convert\HookConvertRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(LoadAllIncludesRector::class);

    $rectorConfig->rule(HookConvertRector::class);
};
