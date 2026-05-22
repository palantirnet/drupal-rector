<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal9\Rector\Property\ProtectedStaticModulesPropertyRector;

use Iterator;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;

final class ProtectedStaticModulesPropertyRectorTest extends AbstractDrupalRectorTestCase
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
        return self::yieldFilesFromDirectory(__DIR__.'/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }
}
