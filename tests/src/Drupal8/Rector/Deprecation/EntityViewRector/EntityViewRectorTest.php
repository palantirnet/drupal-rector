<?php

declare(strict_types=1);

namespace Drupal8\Rector\Deprecation\EntityViewRector;

use Iterator;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;

#[\PHPUnit\Framework\Attributes\CoversFunction('refactor')]
class EntityViewRectorTest extends AbstractDrupalRectorTestCase
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
        // must be implemented
        return __DIR__.'/config/configured_rule.php';
    }
}
