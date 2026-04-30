<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\ReplaceRequestTimeConstantRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(ReplaceRequestTimeConstantRector::class);
};
