<?php

declare(strict_types=1);
use DrupalRector\Drupal9\Rector\Deprecation\ModuleLoadRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // Change record https://www.drupal.org/node/3220952
    $rectorConfig->rule(ModuleLoadRector::class);
};
