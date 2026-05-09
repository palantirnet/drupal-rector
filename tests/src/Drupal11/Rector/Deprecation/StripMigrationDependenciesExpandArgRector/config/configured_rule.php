<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\StripMigrationDependenciesExpandArgRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(StripMigrationDependenciesExpandArgRector::class, $rectorConfig, false, [
        new DrupalIntroducedVersionConfiguration('11.0.0'),
    ]);
};
