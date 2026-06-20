<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use Iterator;

#[\PHPUnit\Framework\Attributes\CoversFunction('refactor')]
class BackwardsCompatibilityPhpUnitTestAnnotationToAttributeRectorTest extends AbstractDrupalRectorTestCase
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
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-next-major');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule_simulate_next_major.php';
    }
}
