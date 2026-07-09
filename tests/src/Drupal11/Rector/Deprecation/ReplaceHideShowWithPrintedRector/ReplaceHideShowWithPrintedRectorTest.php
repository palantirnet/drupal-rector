<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal11\Rector\Deprecation\ReplaceHideShowWithPrintedRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ReplaceHideShowWithPrintedRectorTest extends AbstractDrupalRectorTestCase
{
    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    /**
     * @return \Iterator<<string>>
     */
    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }
}
