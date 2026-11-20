<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\CapturingHookConvertRector;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\HookClassAssertions;

/**
 * A plain `$rectorConfig->rule(HookConvertRector::class)` keeps its old
 * behaviour: the hook body is copied unfixed (use two runs), the rest of the
 * file is processed as usual.
 */
class HookConvertRectorUnwiredTest extends AbstractDrupalRectorTestCase
{
    use HookClassAssertions;

    public function test(): void
    {
        // Rector's shared test container keeps afterResolving() callbacks of
        // earlier test classes, so the rule can arrive wired by a leaked
        // callback from NestedRuleRunner::register(). Undo it: in a real run
        // there is one container per config, so a plain rule() stays unwired.
        $this->make(CapturingHookConvertRector::class)->useNestedRuleRunner(null);

        $this->doTestFileAndHookClass(
            __DIR__.'/fixture-single-run/singlerun/single_run.module.inc',
            __DIR__.'/fixture-single-run/expected/single_run_unwired.php.inc',
        );
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/single_run_unwired.php';
    }
}
