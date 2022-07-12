<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FileDirectoryOsTempRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(FileDirectoryOsTempRector::class);
};
