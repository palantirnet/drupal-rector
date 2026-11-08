<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\DeprecationHelperRemoveRector;
use DrupalRector\Rector\ValueObject\DeprecationHelperRemoveConfiguration;
use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Set\DrupalSetList;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // The composer-based set binds every rule to the drupal/core versions its
    // deprecation applies to, so it fixes what is deprecated on your installed
    // core and nothing newer. This is the config for sites; for a contrib
    // module or theme, copy rector.contrib.php instead.
    //
    // Load the set directly rather than through Rector's
    // withComposerBased(drupal: true): released Rector builds up to 2.8.3
    // scope the class that toggle refers to, so it fails with "Class
    // RectorPrefix...\DrupalRector\Set\DrupalSetList not found".
    $rectorConfig->sets([DrupalSetList::COMPOSER_BASED]);

    // The rewritten code only has to run on the installed core, so
    // backward-compatibility wrapping is off. The composer-based set turns it
    // off as well, and its setting wins over this one. Contrib modules that
    // need DeprecationHelper wrappers use rector.contrib.php instead.
    $rectorConfig->singleton(DrupalRectorSettings::class, fn () => (new DrupalRectorSettings())
        ->disableBackwardCompatibility()
    );

    // To remove DeprecationHelper BC wrappers left in your code by an earlier
    // run, for every deprecation up to your installed Drupal version:
    //
    // $rectorConfig->ruleWithConfiguration(DeprecationHelperRemoveRector::class, [
    //     new DeprecationHelperRemoveConfiguration(\Drupal::VERSION),
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
