<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitAddRunTestsInSeparateProcessesAttributeRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use Iterator;

#[\PHPUnit\Framework\Attributes\CoversFunction('refactor')]
class BelowVersionPhpUnitAddRunTestsInSeparateProcessesAttributeRectorTest extends AbstractDrupalRectorTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    /**
     * @return Iterator<<string>>
     */
    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-below-version');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule_below_version.php';
    }
}
