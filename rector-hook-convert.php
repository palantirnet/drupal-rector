<?php

declare(strict_types=1);

use DrupalRector\Rector\Convert\HookConvertRector;
use Rector\Config\RectorConfig;

/**
 * Standalone configuration for converting procedural hook implementations into
 * OOP hook classes (#[Hook] attributes).
 *
 * IMPORTANT: run this as a SEPARATE rector pass, AFTER your deprecation pass.
 *
 * HookConvertRector moves each hook body into a brand-new `src/Hook/*Hooks.php`
 * file that it writes directly to disk. Rector (2.x) has no way to feed a
 * newly-created file back through the rule pipeline within the same run, so any
 * deprecated API call that lives inside a hook body would NOT be fixed if this
 * rule ran together with the deprecation sets — the unfixed body would be copied
 * into the new class and never revisited.
 *
 * Therefore: do not add this rule to a config that also loads the Drupal
 * deprecation sets. Run two passes instead:
 *
 *   vendor/bin/rector process modules/contrib/my_module                       # pass 1: deprecations
 *   vendor/bin/rector process modules/contrib/my_module \
 *     --config=vendor/palantirnet/drupal-rector/rector-hook-convert.php # pass 2: hook conversion
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(HookConvertRector::class);

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
