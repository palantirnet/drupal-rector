<?php

declare(strict_types=1);

namespace DrupalRector\Tests;

use DrupalRector\Services\DrupalRectorSettings;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

abstract class AbstractDrupalRectorTestCase extends AbstractRectorTestCase
{
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
