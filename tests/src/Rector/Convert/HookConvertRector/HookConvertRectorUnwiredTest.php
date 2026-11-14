<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\CapturingHookConvertRector;
use Rector\PhpParser\Printer\BetterStandardPrinter;

/**
 * A plain `$rectorConfig->rule(HookConvertRector::class)` keeps its old
 * behaviour: the hook body is copied unfixed (use two runs), the rest of the
 * file is processed as usual.
 */
class HookConvertRectorUnwiredTest extends AbstractDrupalRectorTestCase
{
    public function test(): void
    {
        // Rector's shared test container keeps afterResolving() callbacks of
        // earlier test classes, so the rule can arrive wired by a leaked
        // callback from NestedRuleRunner::register(). Undo it: in a real run
        // there is one container per config, so a plain rule() stays unwired.
        $this->make(CapturingHookConvertRector::class)->useNestedRuleRunner(null);

        $this->doTestFile(__DIR__.'/fixture-single-run/singlerun/single_run.module.inc');

        $hookClass = $this->make(CapturingHookConvertRector::class)->printHookClass($this->make(BetterStandardPrinter::class));

        $this->assertStringContainsString('format_size($bytes)', $hookClass);
        $this->assertStringNotContainsString('SizeMarkup', $hookClass);
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/single_run_unwired.php';
    }
}
