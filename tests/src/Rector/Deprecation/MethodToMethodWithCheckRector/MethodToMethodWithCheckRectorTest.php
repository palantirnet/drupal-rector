<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Deprecation\MethodToMethodWithCheckRector;

use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use Iterator;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversFunction('refactor')]
class MethodToMethodWithCheckRectorTest extends AbstractDrupalRectorTestCase
{
    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    /**
     * @return Iterator<<string>>
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
     * @return Iterator<<string>>
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
