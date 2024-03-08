<?php

declare(strict_types=1);

return static function (Rector\Config\RectorConfig $rectorConfig): void {
    // Change record https://www.drupal.org/node/3220952
    $rectorConfig->rule(DrupalRector\Drupal9\Rector\Deprecation\ModuleLoadRector::class);
};
