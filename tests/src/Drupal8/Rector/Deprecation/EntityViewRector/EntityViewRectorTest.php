<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal8\Rector\Deprecation\EntityViewRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use Iterator;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversFunction('refactor')]
class EntityViewRectorTest extends AbstractDrupalRectorTestCase
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

    public function provideConfigFilePath(): string
    {
        // must be implemented
        return __DIR__.'/config/configured_rule.php';
    }
}
