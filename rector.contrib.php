<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\DeprecationHelperRemoveRector;
use DrupalRector\Rector\ValueObject\DeprecationHelperRemoveConfiguration;
use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Set\Drupal10SetList;
use DrupalRector\Set\Drupal11SetList;
use Rector\Config\RectorConfig;

/**
 * Configuration for contrib modules and themes.
 *
 * Copy it to the root of the project you develop the module in, as
 * rector.php:
 *
 *   cp vendor/palantirnet/drupal-rector/rector.contrib.php rector.php
 *
 * A module has to keep working on the Drupal versions it declares in
 * core_version_requirement, not just on the core installed next to it. So this
 * config lists the sets by hand and wraps every rewrite in
 * DeprecationHelper::backwardsCompatibleCall() for deprecations newer than the
 * minimum version you support. Run it against the newest Drupal you support:
 * rules are skipped on a core older than their deprecation.
 *
 * Sites use rector.php instead, which loads the composer-based set.
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        Drupal10SetList::DRUPAL_10,
        Drupal11SetList::DRUPAL_11,
    ]);

    // Set the lowest Drupal version your module supports, i.e. the lower
    // bound of core_version_requirement in its .info.yml.
    $rectorConfig->singleton(DrupalRectorSettings::class, fn () => (new DrupalRectorSettings())
        ->enableBackwardCompatibility()
        ->setMinimumCoreVersionSupported('10.3.0')
    );

    // Once you raise your minimum supported Drupal version, uncomment this
    // rule with the same version to strip DeprecationHelper wrappers for
    // deprecations introduced before it. The wrappers are replaced with the
    // new API call directly.
    //
    // $rectorConfig->ruleWithConfiguration(DeprecationHelperRemoveRector::class, [
    //     new DeprecationHelperRemoveConfiguration('10.3.0'),
    // ]);

    // When phpstan-drupal is available, we should load it to get better type
    // inference to use in rectors.
    $phpstanDrupalExtension = __DIR__.'/vendor/mglaman/phpstan-drupal/extension.neon';
    if (file_exists($phpstanDrupalExtension)) {
        $rectorConfig->phpstanConfigs([$phpstanDrupalExtension]);
    }

    if (class_exists('DrupalFinder\DrupalFinderComposerRuntime')) {
        $drupalFinder = new DrupalFinder\DrupalFinderComposerRuntime();
    } else {
        $drupalFinder = new DrupalFinder\DrupalFinder();
        $drupalFinder->locateRoot(__DIR__);
    }
    $drupalRoot = $drupalFinder->getDrupalRoot();
    $rectorConfig->autoloadPaths([
        $drupalRoot.'/core',
        $drupalRoot.'/modules',
        $drupalRoot.'/profiles',
        $drupalRoot.'/themes',
    ]);

    $rectorConfig->skip(['*/upgrade_status/tests/modules/*']);
    $rectorConfig->fileExtensions(['php', 'module', 'theme', 'install', 'profile', 'inc', 'engine']);
    $rectorConfig->importNames(true, false);
    $rectorConfig->importShortClasses(false);
};
