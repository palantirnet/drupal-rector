<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal10\Rector\Deprecation\SystemTimeZonesRector;

use DrupalRector\Services\DrupalRectorSettings;
use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

#[\PHPUnit\Framework\Attributes\CoversFunction('refactor')]
class SystemTimeZonesRectorTest extends AbstractRectorTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideData')]
    public function testAboveVersion(string $filePath): void
    {
        static::getContainer()->make(DrupalRectorSettings::class)->setDrupalVersion('99.99.99');
        try {
            $this->doTestFile($filePath);
        } finally {
            static::getContainer()->make(DrupalRectorSettings::class)->setDrupalVersion(null);
        }
    }

    /**
     * @return Iterator<<string>>
     */
    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture');
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideDataBelowVersion')]
    public function testBelowVersion(string $filePath): void
    {
        static::getContainer()->make(DrupalRectorSettings::class)->setDrupalVersion('1.0.0');
        try {
            $this->doTestFile($filePath);
        } finally {
            static::getContainer()->make(DrupalRectorSettings::class)->setDrupalVersion(null);
        }
    }

    /**
     * @return \Iterator<<string>>
     */
    public static function provideDataBelowVersion(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-below-version');
    }

    public function provideConfigFilePath(): string
    {
        // must be implemented
        return __DIR__.'/config/configured_rule.php';
    }
}
