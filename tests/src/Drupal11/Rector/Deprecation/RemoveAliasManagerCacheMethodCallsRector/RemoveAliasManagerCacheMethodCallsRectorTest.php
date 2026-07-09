<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal11\Rector\Deprecation\RemoveAliasManagerCacheMethodCallsRector;

use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class RemoveAliasManagerCacheMethodCallsRectorTest extends AbstractDrupalRectorTestCase
{
    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        // Backward compatibility disabled: the deprecated call is removed.
        static::getContainer()->make(DrupalRectorSettings::class)->disableBackwardCompatibility();
        $this->doTestFile($filePath);
    }

    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture');
    }

    #[DataProvider('provideDataBelowVersion')]
    public function testBelowVersion(string $filePath): void
    {
        // Target Drupal is below the 11.3.0 deprecation: the rector must not
        // fire, regardless of the backward-compatibility setting.
        static::getContainer()->make(DrupalRectorSettings::class)->setDrupalVersion('11.2.0');
        $this->doTestFile($filePath);
    }

    public static function provideDataBelowVersion(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-below-version');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }
}
