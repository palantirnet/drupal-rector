<?php

declare(strict_types=1);

use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\Rector\AbstractDrupalCoreRector\Stub\ClassConstFetchBCRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->singleton(DrupalRectorSettings::class, fn () => (new DrupalRectorSettings())->setMinimumCoreVersionSupported('10.1.0'));

    $rectorConfig->ruleWithConfiguration(ClassConstFetchBCRector::class, [
        new DrupalIntroducedVersionConfiguration('11.0.0'),
    ]);
};
