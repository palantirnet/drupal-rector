<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\ReplaceRequestTimeConstantRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(ReplaceRequestTimeConstantRector::class, $rectorConfig, false, [
        new DrupalIntroducedVersionConfiguration('11.0.0'),
    ]);
};
