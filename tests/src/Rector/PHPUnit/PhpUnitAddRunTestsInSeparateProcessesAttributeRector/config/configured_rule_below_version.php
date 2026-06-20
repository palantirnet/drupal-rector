<?php

declare(strict_types=1);

use DrupalRector\Rector\PHPUnit\PhpUnitAddRunTestsInSeparateProcessesAttributeRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(PhpUnitAddRunTestsInSeparateProcessesAttributeRector::class, $rectorConfig, false, [
        new DrupalIntroducedVersionConfiguration('12.0.0'),
    ]);
};
