<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\AbstractDrupalCoreRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class AbstractDrupalCoreRectorBcTest extends AbstractRectorTestCase
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
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-bc');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule_bc.php';
    }
}
