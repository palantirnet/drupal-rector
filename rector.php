<?php

declare(strict_types=1);

use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Set\Drupal10SetList;
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
    ]);

    // Configure DrupalRectorSettings to control rule behaviour.
    // By default, backward-compatibility wrapping is disabled (recommended for
    // projects that target a single Drupal version). Enable it if you need to
    // support multiple Drupal versions simultaneously.
    $rectorConfig->singleton(DrupalRectorSettings::class, fn () =>
        (new DrupalRectorSettings())
            ->disableBackwardCompatibility()
            // Contrib module developers: set the minimum Drupal version your
            // module needs to support so that BC wrappers are emitted correctly.
            // Example: ->setMinimumCoreVersionSupported('10.5.0')
    );
    $rectorConfig->afterResolving(
        AbstractDrupalCoreRector::class,
        fn ($rector, $container) => $rector->setDrupalRectorSettings($container->make(DrupalRectorSettings::class))
    );

    if (class_exists('DrupalFinder\DrupalFinderComposerRuntime')) {
        $drupalFinder = new DrupalFinder\DrupalFinderComposerRuntime();
    } else {
        $drupalFinder = new DrupalFinder\DrupalFinder();
        $drupalFinder->locateRoot(__DIR__);
    }
    $drupalRoot = $drupalFinder->getDrupalRoot();
    $rectorConfig->autoloadPaths([
        $drupalRoot . '/core',
        $drupalRoot . '/modules',
        $drupalRoot . '/profiles',
        $drupalRoot . '/themes'
    ]);

    $rectorConfig->skip(['*/upgrade_status/tests/modules/*']);
    $rectorConfig->fileExtensions(['php', 'module', 'theme', 'install', 'profile', 'inc', 'engine']);
    $rectorConfig->importNames(true, false);
    $rectorConfig->importShortClasses(false);
};
