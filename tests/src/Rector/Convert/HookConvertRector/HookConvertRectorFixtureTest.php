<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\HookClassAssertions;
use PHPUnit\Framework\Attributes\DataProvider;

class HookConvertRectorFixtureTest extends AbstractDrupalRectorTestCase
{
    use HookClassAssertions;

    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFileAndHookClass($filePath, __DIR__.'/expected/hookconvertrector/'.basename($filePath, '.module.inc').'.php.inc');
    }

    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture', '*.module.inc');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }
}
