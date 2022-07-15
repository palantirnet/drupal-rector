<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\EntityDeleteMultipleRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(EntityDeleteMultipleRector::class);
};
