<?php

declare(strict_types=1);

namespace Drupal9\Rector\Deprecation\AssertNoUniqueTextRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

#[\PHPUnit\Framework\Attributes\CoversFunction('refactor')]
class AssertNoUniqueTextRector extends AbstractRectorTestCase
{
    public function __construct()
    {
        parent::__construct(static::class);
    }

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
