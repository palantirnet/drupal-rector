<?php

declare(strict_types=1);

use DrupalFinder\DrupalFinder;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // @todo find out how to only load the relevant rector rules.
    //   Should we try and load \Drupal::VERSION and check?
    $rectorConfig->import(__DIR__ .  '/vendor/palantirnet/drupal-rector/config/drupal-8/drupal-8-all-deprecations.php');
    $rectorConfig->import(__DIR__ .  '/vendor/palantirnet/drupal-rector/config/drupal-9/drupal-9-all-deprecations.php');

    $parameters = $rectorConfig->parameters();

    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(__DIR__);
    $drupalRoot = $drupalFinder->getDrupalRoot();
    $rectorConfig->autoloadPaths([
        $drupalRoot . '/core',
        $drupalRoot . '/modules',
        $drupalRoot . '/profiles',
        $drupalRoot . '/themes'
    ]);

    $rectorConfig->skip(['*/upgrade_status/tests/modules/*']);
    $rectorConfig->fileExtensions(['php', 'module', 'theme', 'install', 'profile', 'inc', 'engine']);
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);
    $parameters->set('drupal_rector_notices_as_comments', true);
};
