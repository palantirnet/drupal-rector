<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\ReplaceRequestTimeConstantRector;
use DrupalRector\Drupal11\Rector\Deprecation\GetNameToNameRector;
use DrupalRector\Drupal11\Rector\Deprecation\MigrateSqlGetMigrationPluginManagerRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveStateCacheSettingRector;
use DrupalRector\Drupal11\Rector\Deprecation\StripMigrationDependenciesExpandArgRector;
use DrupalRector\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector;
use DrupalRector\Rector\PHPUnit\ValueObject\PhpUnitTestAnnotationToAttributeConfiguration;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3217904
    // TestCase::getName() deprecated in drupal:10.1.0, removed in drupal:11.0.0.
    // Replaced by name().
    $rectorConfig->rule(GetNameToNameRector::class);

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

    // https://www.drupal.org/node/3417066 (@group legacy → #[IgnoreDeprecations])
    // https://www.drupal.org/project/drupal/issues/3535662 (annotations → attributes)
    // PHPUnit 12 (Drupal 12) drops annotation metadata in favour of attributes.
    // Backward-compatible: under BC-on / Drupal < 12 the annotation is kept
    // alongside the new attribute (unknown attribute classes are ignored on
    // PHPUnit 9/10/11); only a D12 install or an opted-in clean rewrite strips it.
    $rectorConfig->ruleWithConfiguration(PhpUnitTestAnnotationToAttributeRector::class, [
        new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'group', 'PHPUnit\Framework\Attributes\Group'),
        new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'dataProvider', 'PHPUnit\Framework\Attributes\DataProvider'),
        new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'depends', 'PHPUnit\Framework\Attributes\Depends'),
        new PhpUnitTestAnnotationToAttributeConfiguration('11.0.0', '12.0.0', 'testWith', 'PHPUnit\Framework\Attributes\TestWith'),
    ]);
};
