<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\ReplaceViewsProceduralFunctionsRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(ReplaceViewsProceduralFunctionsRector::class, $rectorConfig, false, [
        new DrupalIntroducedVersionConfiguration('11.4.0'),
    ]);
};
