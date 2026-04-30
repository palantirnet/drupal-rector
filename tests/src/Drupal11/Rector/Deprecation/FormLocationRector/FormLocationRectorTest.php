<?php

declare(strict_types=1);

namespace Drupal11\Rector\Deprecation\FormLocationRector;

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class FormLocationRectorTest extends AbstractRectorTestCase
{
    /**
     * @covers ::refactor
     *
     * @dataProvider provideData
     */
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
