<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\RequestTimeConstRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(RequestTimeConstRector::class);
};
