<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\SystemTimeZonesRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(SystemTimeZonesRector::class, $rectorConfig, false, [
        new DrupalIntroducedVersionConfiguration('10.1.0'),
    ]);
};
