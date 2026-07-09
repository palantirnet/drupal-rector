<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\AbstractDrupalCoreRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;

class AbstractDrupalCoreRectorBcTest extends AbstractDrupalRectorTestCase
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
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-bc');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule_bc.php';
    }
}
