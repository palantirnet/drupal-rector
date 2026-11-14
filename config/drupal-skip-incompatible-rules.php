<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\PHPUnit60\Rector\ClassMethod\AddDoesNotPerformAssertionToNonAssertingTestRector;
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

    // Adds #[DoesNotPerformAssertions] to test methods it thinks don't assert.
    // It only looks inside the test method, but in Drupal tests often assert
    // elsewhere: in PHPUnit lifecycle methods such as setUp(), tearDown() or
    // assertPostConditions() of the test class or a base class (e.g. core's
    // PackageManagerKernelTestBase and KernelTestBaseShutdownTest), via
    // parent::testX() calls, through expectUserDeprecationMessage() (each
    // expected deprecation counts as an assertion), or through helpers it can't
    // resolve. Core runs PHPUnit with failOnRisky="true", so those tests fail
    // with "This test is not expected to perform assertions but performed N
    // assertions".
    // The upstream fix for https://github.com/rectorphp/rector/issues/9965
    // (https://github.com/rectorphp/rector-phpunit/pull/822) covers parent::,
    // expectUserDeprecationMessage() and lifecycle methods. Assertions in
    // helpers the rule can't resolve (dynamic calls, deep call chains, helpers
    // not named assert*) stay invisible, and a wrong attribute fails the test
    // run, so this skip is meant to stay after that fix is released.
    // https://www.drupal.org/i/3638148
    if (class_exists(AddDoesNotPerformAssertionToNonAssertingTestRector::class)) {
        $rectorConfig->skip([AddDoesNotPerformAssertionToNonAssertingTestRector::class]);
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
