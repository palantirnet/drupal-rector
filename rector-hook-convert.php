<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

/**
 * Standalone configuration for converting procedural hook implementations into
 * OOP hook classes (#[Hook] attributes).
 *
 * Two ways to use it:
 *
 * 1. Same run as the deprecation rules (recommended). Rector visits a function
 *    before the code inside it, and HookConvertRector copies the hook body into
 *    a new hook class file right then, so deprecation rules would never see that
 *    copy. This config (via config/hook-convert.php) wires in the container so
 *    HookConvertRector first runs the other active rules over each hook body.
 *    Combine it with your deprecation set in one config:
 *
 *      return RectorConfig::configure()
 *          ->withSets([DrupalSetList::COMPOSER_BASED, DrupalSetList::HOOK_CONVERT]);
 *
 *    Remaining difference: the hook class keeps fully-qualified class names
 *    (Rector's name-import step only runs on the procedural file).
 *
 * 2. As a separate second pass, AFTER the deprecation pass, with this file as
 *    the config:
 *
 *      vendor/bin/rector process modules/contrib/my_module                       # pass 1: deprecations
 *      vendor/bin/rector process modules/contrib/my_module \
 *        --config=vendor/palantirnet/drupal-rector/rector-hook-convert.php # pass 2: hook conversion
 *
 * Registering HookConvertRector with a plain `$rectorConfig->rule()` (without
 * config/hook-convert.php) only supports option 2.
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__.'/config/hook-convert.php');

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
