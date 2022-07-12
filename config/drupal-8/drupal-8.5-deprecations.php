<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\DatetimeDateStorageFormatRector;
use DrupalRector\Rector\Deprecation\DatetimeDatetimeStorageFormatRector;
use DrupalRector\Rector\Deprecation\DatetimeStorageTimezoneRector;
use DrupalRector\Rector\Deprecation\DrupalSetMessageRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(DrupalSetMessageRector::class, [
        'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
    ]);
    $rectorConfig->rule(DatetimeDateStorageFormatRector::class);
    $rectorConfig->rule(DatetimeDatetimeStorageFormatRector::class);
    $rectorConfig->rule(DatetimeStorageTimezoneRector::class);
};
