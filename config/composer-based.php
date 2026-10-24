<?php

declare(strict_types=1);

use DrupalRector\Set\ComposerBasedSetResolver;
use Rector\Composer\InstalledPackageResolver;
use Rector\Config\RectorConfig;

/**
 * Composer-based Drupal set, see \DrupalRector\Set\DrupalSetList::COMPOSER_BASED.
 *
 * The installed version is resolved the same way Rector resolves it for its
 * own composer-bound rules.
 */
return static function (RectorConfig $rectorConfig): void {
    $installedCoreVersion = (new InstalledPackageResolver())->resolvePackageVersion('drupal/core');

    $rectorConfig->sets((new ComposerBasedSetResolver())->resolve($installedCoreVersion));
};
