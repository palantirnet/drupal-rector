<?php

declare(strict_types=1);

namespace DrupalRector\Tests;

use DrupalRector\Services\DrupalRectorSettings;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

abstract class AbstractDrupalRectorTestCase extends AbstractRectorTestCase
{
    /**
     * Rules bonded through ComposerPackageConstraintInterface are skipped when
     * the installed drupal/core does not satisfy their constraint — and this
     * package does not require drupal/core at all, so they would all be skipped
     * here. Read the version from a stub composer.json instead.
     *
     * A rule states a closed range, so no single version can activate them all:
     * a rule for an API removed in Drupal 10 caps at `<11.0.0`, while one
     * deprecated in 11.4 only starts at `>=11.4.0`. Pin each `Drupal<major>`
     * test namespace to its own major instead, high within that major so every
     * rule in the set is active. Tests outside those namespaces keep the
     * fallback pin.
     */
    protected function provideComposerJsonFilePath(): ?string
    {
        if (preg_match('#\\\\Tests\\\\Drupal(\d+)\\\\#', static::class, $matches) !== 1) {
            return __DIR__.'/../composer-json/drupal-core-installed.json';
        }

        return __DIR__.'/../composer-json/drupal-core-'.$matches[1].'.json';
    }

    protected function tearDown(): void
    {
        // The Rector test container is shared across tests in the same class,
        // so DrupalRectorSettings mutations (e.g. setDrupalVersion) leak unless
        // explicitly reset. Restore the class defaults after every test.
        static::getContainer()->make(DrupalRectorSettings::class)
            ->setDrupalVersion(null)
            ->enableBackwardCompatibility()
            ->setMinimumCoreVersionSupported('10.1.0');

        parent::tearDown();
    }
}
