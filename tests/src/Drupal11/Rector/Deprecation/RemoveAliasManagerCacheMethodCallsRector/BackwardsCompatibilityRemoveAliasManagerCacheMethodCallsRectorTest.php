<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal11\Rector\Deprecation\RemoveAliasManagerCacheMethodCallsRector;

use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;

class BackwardsCompatibilityRemoveAliasManagerCacheMethodCallsRectorTest extends AbstractDrupalRectorTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideData')]
    public function test(string $filePath): void
    {
        // Backward compatibility enabled with a minimum supported core version
        // below 11.3.0: the deprecated call is wrapped in a
        // DeprecationHelper::backwardsCompatibleCall() with a no-op current
        // callable.
        static::getContainer()->make(DrupalRectorSettings::class)
            ->enableBackwardCompatibility()
            ->setMinimumCoreVersionSupported('10.1.0');
        $this->doTestFile($filePath);
    }

    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-bc');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule_bc.php';
    }
}
