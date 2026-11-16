<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal11\Rector\Deprecation\ReplacePdoFetchConstantsRector;

use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ReplacePdoFetchConstantsRectorTest extends AbstractDrupalRectorTestCase
{
    #[DataProvider('provideData')]
    public function testAboveVersion(string $filePath): void
    {
        static::getContainer()->make(DrupalRectorSettings::class)->setDrupalVersion('99.99.99');
        $this->doTestFile($filePath);
    }

    /**
     * @return \Iterator<<string>>
     */
    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture');
    }

    #[DataProvider('provideDataBelowVersion')]
    public function testBelowVersion(string $filePath): void
    {
        static::getContainer()->make(DrupalRectorSettings::class)->setDrupalVersion('1.0.0');
        $this->doTestFile($filePath);
    }

    /**
     * @return \Iterator<<string>>
     */
    public static function provideDataBelowVersion(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-below-version');
    }

    #[DataProvider('provideDataMinimumCoreAtIntroducedVersion')]
    public function testMinimumCoreAtIntroducedVersion(string $filePath): void
    {
        // No backwards compatibility needed: plain FetchAs replacement.
        static::getContainer()->make(DrupalRectorSettings::class)
            ->setDrupalVersion('99.99.99')
            ->setMinimumCoreVersionSupported('11.2.0');
        $this->doTestFile($filePath);
    }

    /**
     * @return \Iterator<<string>>
     */
    public static function provideDataMinimumCoreAtIntroducedVersion(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-min-core-11-2');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }
}
