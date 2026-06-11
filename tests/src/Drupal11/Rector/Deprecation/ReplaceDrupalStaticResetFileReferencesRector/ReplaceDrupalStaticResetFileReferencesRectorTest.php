<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal11\Rector\Deprecation\ReplaceDrupalStaticResetFileReferencesRector;

use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;

class ReplaceDrupalStaticResetFileReferencesRectorTest extends AbstractDrupalRectorTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideData')]
    public function testAboveVersion(string $filePath): void
    {
        static::getContainer()->make(DrupalRectorSettings::class)->setDrupalVersion('99.99.99');
        $this->doTestFile($filePath);
    }

    /** @return \Iterator<array<string>> */
    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture');
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideDataBelowVersion')]
    public function testBelowVersion(string $filePath): void
    {
        static::getContainer()->make(DrupalRectorSettings::class)->setDrupalVersion('1.0.0');
        $this->doTestFile($filePath);
    }

    /** @return \Iterator<array<string>> */
    public static function provideDataBelowVersion(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-below-version');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }
}
