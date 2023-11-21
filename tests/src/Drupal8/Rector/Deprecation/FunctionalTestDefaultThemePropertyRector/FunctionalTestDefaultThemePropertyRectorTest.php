<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal8\Rector\Deprecation\FunctionalTestDefaultThemePropertyRector;

use DrupalRector\Tests\Rector\Class_\FunctionalTestDefaultThemePropertyRector\Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class FunctionalTestDefaultThemePropertyRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
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
