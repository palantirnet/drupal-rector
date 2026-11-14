<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\CodeQuality\Rector\Class_\EventSubscriberToAttributeRector;
use Rector\Symfony\Symfony61\Rector\Class_\CommandConfigureToAttributeRector;

/*
 * Skips third-party Rector rules that produce code Drupal cannot run.
 *
 * Rector's own composer-based sets (e.g. `withComposerBased(symfony: true)`)
 * activate on the Symfony packages Drupal core depends on, but some of their
 * rewrites assume the full Symfony framework. `skip()` is additive, so this
 * merges with the project's own `withSkip()` list.
 */
return static function (RectorConfig $rectorConfig): void {
    // Drupal registers event subscribers through the `event_subscriber` service
    // tag (core's RegisterEventSubscribersPass, wrapping Symfony's
    // RegisterListenersPass), which requires EventSubscriberInterface; core has
    // no #[AsEventListener] support. The rewrite fatals the container rebuild:
    // 'Service "…" must implement interface "…\EventSubscriberInterface"'.
    // https://github.com/palantirnet/drupal-rector/issues/446
    if (class_exists(EventSubscriberToAttributeRector::class)) {
        $rectorConfig->skip([EventSubscriberToAttributeRector::class]);
    }

    // Moves setDescription()/setAliases()/setHidden() from configure() into
    // #[AsCommand(...)] without making sure the attribute gets a name. A command
    // that names itself through the constructor, like core's ServerCommand
    // (parent::__construct('server')), ends up with
    // #[AsCommand(description: '…')], and AsCommand's required $name makes the
    // console fatal: 'AsCommand::__construct(): Argument #1 ($name) not passed'.
    // Remove this skip once https://github.com/rectorphp/rector-symfony/pull/1102
    // is merged and released, and drupal-rector requires that Rector version.
    // https://www.drupal.org/i/3638148
    if (class_exists(CommandConfigureToAttributeRector::class)) {
        $rectorConfig->skip([CommandConfigureToAttributeRector::class]);
    }
};
