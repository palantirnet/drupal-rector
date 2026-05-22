<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\DeprecationHelperRemoveRector;
use DrupalRector\Rector\ValueObject\DeprecationHelperRemoveConfiguration;
use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Set\Drupal10SetList;
use DrupalRector\Set\Drupal11SetList;
use DrupalRector\Set\Drupal8SetList;
use DrupalRector\Set\Drupal9SetList;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // Adjust the set lists to be more granular to your Drupal requirements.
    // @todo find out how to only load the relevant rector rules.
    //   Should we try and load \Drupal::VERSION and check?
    //   new possible option with ComposerTriggeredSet
    //   https://github.com/rectorphp/rector-src/blob/b5a5739b7d7dde621053adff113449860ed5331f/src/Set/ValueObject/ComposerTriggeredSet.php
    $rectorConfig->sets([
        Drupal8SetList::DRUPAL_8,
        Drupal9SetList::DRUPAL_9,
        Drupal10SetList::DRUPAL_10,
        Drupal11SetList::DRUPAL_11,
    ]);

    // Configure DrupalRectorSettings to control rule behaviour.
    // By default, backward-compatibility wrapping is disabled (recommended for
    // projects that target a single Drupal version). Enable it if you need to
    // support multiple Drupal versions simultaneously.
    $rectorConfig->singleton(DrupalRectorSettings::class, fn () => (new DrupalRectorSettings())
            ->disableBackwardCompatibility()
        // Contrib module developers: set the minimum Drupal version your
        // module needs to support so that BC wrappers are emitted correctly.
        // Example: ->setMinimumCoreVersionSupported('10.5.0')
    );

    // Contrib modules: once you raise your minimum supported Drupal version,
    // uncomment and configure this rule to strip DeprecationHelper BC wrappers
    // for any deprecation introduced before that version. The wrappers are
    // replaced with the new API call directly.
    //
    // $rectorConfig->ruleWithConfiguration(DeprecationHelperRemoveRector::class, [
    //     new DeprecationHelperRemoveConfiguration('10.3.0'),
    // ]);

    // When phsptan-drupal is available, we should load it to get better type
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
