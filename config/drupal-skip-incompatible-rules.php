<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\CodeQuality\Rector\Class_\EventSubscriberToAttributeRector;

/**
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
};
