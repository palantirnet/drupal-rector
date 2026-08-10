<?php

declare(strict_types=1);

use DrupalRector\Services\DrupalRectorSettings;
use Rector\Config\RectorConfig;

/**
 * Composer-based selection defaults: PHPUnit bootstrap + settings.
 *
 * Standalone set imported by `config/composer-based.php` (see
 * \DrupalRector\Set\DrupalSetList::COMPOSER_BASED). The per-minor deprecation
 * configs do not register the bootstrap themselves — only the aggregated
 * `drupal-{10,11}-all-deprecations.php` sets do.
 *
 * Kept separate from the deprecation configs on purpose: the bootstrap file
 * throws when it cannot detect a Drupal installation, and the composer-based
 * set only ever activates rules when drupal/core is installed, so the throw can
 * never fire here. Folding it into the shared per-minor configs would change
 * behaviour for users who load a single set manually against a non-Drupal
 * project.
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->bootstrapFiles([
        __DIR__.'/drupal-phpunit-bootstrap-file.php',
    ]);

    // Composer-based selection pins the rules to the exact installed Drupal
    // version and only loads sets for deprecations that are live on it, so the
    // rewritten code only ever runs against that one version. There is no older
    // minor to stay compatible with, which makes the DeprecationHelper BC
    // wrappers pure noise here — disable them by default. (A project that does
    // need them can re-register this singleton in its own rector.php.)
    $rectorConfig->singleton(DrupalRectorSettings::class, fn () => (new DrupalRectorSettings())
        ->disableBackwardCompatibility());

    // Drupal executes PHP from several non-.php extensions.
    $rectorConfig->fileExtensions(['php', 'module', 'theme', 'install', 'profile', 'inc', 'engine']);

    // upgrade_status ships intentionally broken test modules.
    $rectorConfig->skip(['*/upgrade_status/tests/modules/*']);

    // Autoloading and phpstan-drupal only make sense when Drupal is actually
    // present. Bail out otherwise — DrupalFinderComposerRuntime::getDrupalRoot()
    // calls Composer\InstalledVersions::getInstallPath('drupal/core'), which
    // THROWS (not returns null) when the package is not installed, so this must
    // be guarded before the lookup.
    if (! \Composer\InstalledVersions::isInstalled('drupal/core')) {
        return;
    }

    $drupalFinder = new \DrupalFinder\DrupalFinderComposerRuntime();

    $drupalRoot = $drupalFinder->getDrupalRoot();
    if (is_string($drupalRoot) && $drupalRoot !== '') {
        $rectorConfig->autoloadPaths([
            $drupalRoot.'/core',
            $drupalRoot.'/modules',
            $drupalRoot.'/profiles',
            $drupalRoot.'/themes',
        ]);
    }

    // phpstan-drupal lives in the analysed project's vendor dir, not ours.
    $vendorDir = $drupalFinder->getVendorDir();
    if (is_string($vendorDir) && $vendorDir !== '') {
        $phpstanDrupalExtension = $vendorDir.'/mglaman/phpstan-drupal/extension.neon';
        if (file_exists($phpstanDrupalExtension)) {
            $rectorConfig->phpstanConfigs([$phpstanDrupalExtension]);
        }
    }
};
