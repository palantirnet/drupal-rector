<?php

declare(strict_types=1);

use DrupalRector\Drupal9\Rector\Deprecation\FileBuildUriRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(FileBuildUriRector::class);
};
