<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

/**
 * Drupal infrastructure config, auto-loaded for every project that requires
 * drupal-rector ("type": "rector-extension" + "extra.rector.includes").
 *
 * It supplies the Drupal-specific Rector plumbing that otherwise has to be
 * copied into every project's rector.php: the extra PHP file extensions, class
 * import conventions, the upgrade_status skip, Drupal autoloading and (when
 * available) phpstan-drupal.
 *
 * Loading order is: Rector core defaults -> this file -> the project's
 * rector.php. Because the project's config is applied last it always wins:
 * fileExtensions(), autoloadPaths() and importNames() use "last call wins"
 * semantics, while skip() and phpstanConfigs() are additive. So everything here
 * is a default the project can still override.
 *
 * Imported via $rectorConfig->import(), hence the RectorConfig closure form
 * rather than RectorConfig::configure().
 *
 * NOTE: this deliberately does NOT register config/drupal-phpunit-bootstrap-file.php.
 * That bootstrap throws when it cannot detect a Drupal installation, which would
 * break Rector in any non-Drupal context. The bootstrap is registered by the
 * deprecation sets instead (see \DrupalRector\Set\DrupalSetProvider and the
 * *-all-deprecations sets), which only run against an actual Drupal codebase.
 */
return static function (RectorConfig $rectorConfig): void {
    // Drupal executes PHP from several non-.php extensions.
    $rectorConfig->fileExtensions(['php', 'module', 'theme', 'install', 'profile', 'inc', 'engine']);

    // Drupal convention: import class names, but leave docblock names untouched.
    $rectorConfig->importNames(true, false);
    $rectorConfig->importShortClasses(false);

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
