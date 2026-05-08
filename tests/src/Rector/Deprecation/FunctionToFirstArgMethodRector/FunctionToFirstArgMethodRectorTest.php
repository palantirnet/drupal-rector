<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Deprecation\FunctionToFirstArgMethodRector;

use DrupalRector\Rector\AbstractDrupalCoreRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class FunctionToFirstArgMethodRectorTest extends AbstractRectorTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideData')]
    public function testAboveVersion(string $filePath): void
    {
        AbstractDrupalCoreRector::setVersionOverride('99.99.99');
        try {
            $this->doTestFile($filePath);
        } finally {
            AbstractDrupalCoreRector::setVersionOverride(null);
        }
    }

    /**
     * @return \Iterator<<string>>
     */
    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture');
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideDataBelowVersion')]
    public function testBelowVersion(string $filePath): void
    {
        AbstractDrupalCoreRector::setVersionOverride('1.0.0');
        try {
            $this->doTestFile($filePath);
        } finally {
            AbstractDrupalCoreRector::setVersionOverride(null);
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
        return __DIR__.'/config/configured_rule.php';
    }
}
