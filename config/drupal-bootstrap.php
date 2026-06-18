<?php

declare(strict_types=1);

use DrupalRector\Services\DrupalRectorSettings;
use Rector\Config\RectorConfig;

/**
 * Composer-based selection defaults: PHPUnit bootstrap + settings.
 *
 * Standalone set used by composer-based selection (see
 * \DrupalRector\Set\DrupalSetProvider). The per-minor deprecation configs do
 * not register the bootstrap themselves — only the aggregated
 * `drupal-{10,11}-all-deprecations.php` sets do. Composer-based selection loads
 * the per-minor configs directly, so this set is matched once per Drupal major
 * (drupal/core ^10.0 and ^11.0) to guarantee the defaults below are present.
 *
 * Kept separate from the deprecation configs on purpose: the bootstrap file
 * throws when it cannot detect a Drupal installation, and composer-based sets
 * only ever load when drupal/core is installed, so the throw can never fire
 * here. Folding it into the shared per-minor configs would change behaviour for
 * users who load a single set manually against a non-Drupal project.
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
};
