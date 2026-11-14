<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\CapturingHookConvertRector;
use Rector\PhpParser\Printer\BetterStandardPrinter;

/**
 * Rector visits a hook function before the code inside it, so the deprecation
 * rules used to run only after HookConvertRector copied the body into the hook
 * class. With the container wired in (NestedRuleRunner::register()) the body is
 * fixed first, by every rule and by chains of rules.
 */
class HookConvertRectorSingleRunTest extends AbstractDrupalRectorTestCase
{
    public function test(): void
    {
        $this->doTestFile(__DIR__.'/fixture-single-run/singlerun/single_run.module.inc');

        $hookClass = $this->make(CapturingHookConvertRector::class)->printHookClass($this->make(BetterStandardPrinter::class));

        // The hook body was fixed by both rules (format_size -> ByteSizeMarkup
        // -> SizeMarkup), and exactly once.
        $this->assertStringContainsString('SizeMarkup::create($bytes)', $hookClass);
        $this->assertStringNotContainsString('ByteSizeMarkup', $hookClass);
        $this->assertSame(1, substr_count($hookClass, 'backwardsCompatibleCall'));
        $this->assertStringNotContainsString('singlerun_helper', $hookClass);
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/single_run.php';
    }
}
