<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\MigrateSqlGetMigrationPluginManagerRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(MigrateSqlGetMigrationPluginManagerRector::class, $rectorConfig, false);
};
