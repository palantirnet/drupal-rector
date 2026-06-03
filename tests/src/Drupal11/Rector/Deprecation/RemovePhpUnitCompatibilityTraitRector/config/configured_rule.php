<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\RemovePhpUnitCompatibilityTraitRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(RemovePhpUnitCompatibilityTraitRector::class, $rectorConfig, false, [
        new DrupalIntroducedVersionConfiguration('12.0.0'),
    ]);
};
