<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\UnicodeStrlenRector;
use DrupalRector\Rector\Deprecation\UnicodeStrtolowerRector;
use DrupalRector\Rector\Deprecation\UnicodeSubstrRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(UnicodeStrlenRector::class);
    $rectorConfig->rule(UnicodeStrtolowerRector::class);
    $rectorConfig->rule(UnicodeSubstrRector::class);
};
