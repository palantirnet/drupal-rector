<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal11\Rector\Deprecation\MovePointerToMouseOverRector;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class MovePointerToMouseOverRectorTest extends AbstractRectorTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }
}
