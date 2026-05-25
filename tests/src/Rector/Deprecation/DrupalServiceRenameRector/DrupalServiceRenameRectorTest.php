<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Deprecation\DrupalServiceRenameRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use Iterator;

class DrupalServiceRenameRectorTest extends AbstractDrupalRectorTestCase
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
        return self::yieldFilesFromDirectory(__DIR__.'/fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }
}
