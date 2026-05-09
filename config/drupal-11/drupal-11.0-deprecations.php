<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\ReplaceRequestTimeConstantRector;
use DrupalRector\Drupal11\Rector\Deprecation\MigrateSqlGetMigrationPluginManagerRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveStateCacheSettingRector;
use DrupalRector\Drupal11\Rector\Deprecation\StripMigrationDependenciesExpandArgRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3436954
    // https://www.drupal.org/node/2575105 (change record)
    // $settings['state_cache'] deprecated in drupal:11.0.0.
    // State caching is now permanently enabled and the setting has no effect.
    $rectorConfig->rule(RemoveStateCacheSettingRector::class);

    // https://www.drupal.org/node/3395986
    // REQUEST_TIME constant deprecated in drupal:8.3.0, removed in drupal:11.0.0.
    // Replaced by \Drupal::time()->getRequestTime().
    $rectorConfig->ruleWithConfiguration(ReplaceRequestTimeConstantRector::class, [
        new DrupalIntroducedVersionConfiguration('11.0.0'),
    ]);

    // https://www.drupal.org/node/3574717
    // https://www.drupal.org/node/3442785 (change record)
    // getMigrationDependencies($expand) deprecated in drupal:11.0.0, removed in drupal:12.0.0.
    // The $expand boolean argument is removed; call without arguments.
    $rectorConfig->ruleWithConfiguration(StripMigrationDependenciesExpandArgRector::class, [
        new DrupalIntroducedVersionConfiguration('11.0.0'),
    ]);

    // https://www.drupal.org/node/3439369
    // https://www.drupal.org/node/3282894 (change record)
    // Sql::getMigrationPluginManager() deprecated in drupal:9.5.0, removed in drupal:11.0.0.
    // Replaced by $this->migrationPluginManager property access.
    $rectorConfig->ruleWithConfiguration(MigrateSqlGetMigrationPluginManagerRector::class, [
        new DrupalIntroducedVersionConfiguration('11.0.0'),
    ]);
};
