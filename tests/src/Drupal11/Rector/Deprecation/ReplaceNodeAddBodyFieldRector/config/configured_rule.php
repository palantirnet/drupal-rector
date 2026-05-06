<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeAddBodyFieldRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(ReplaceNodeAddBodyFieldRector::class, $rectorConfig, false, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);
};
