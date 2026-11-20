<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\HookClassAssertions;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Rector visits a hook function before the code inside it, so the deprecation
 * rules used to run only after HookConvertRector copied the body into the hook
 * class. With the container wired in (NestedRuleRunner::register()) the body is
 * fixed first, by every rule and by chains of rules.
 *
 * single_run: the hook body is fixed by both rules (format_size ->
 * ByteSizeMarkup -> SizeMarkup), exactly once, and the helper that is not a hook
 * stays out of the class. translation: t() in the body makes the class use
 * StringTranslationTrait, next to the nested fix.
 */
class HookConvertRectorSingleRunTest extends AbstractDrupalRectorTestCase
{
    use HookClassAssertions;

    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFileAndHookClass($filePath, __DIR__.'/fixture-single-run/expected/'.basename($filePath, '.module.inc').'.php.inc');
    }

    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-single-run', '*.module.inc');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/single_run.php';
    }
}
