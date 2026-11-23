<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub;

use DrupalRector\Rector\Convert\HookConvertRector;
use Rector\PhpParser\Printer\BetterStandardPrinter;

/**
 * Keeps the generated hook class in memory instead of writing it to disk.
 *
 * HookConvertRector::__destruct() writes src/Hook/<Class>.php and a services
 * YAML file. Those writes are irrelevant to the transformation under test and
 * would pollute the fixture directory, so this subclass skips them and lets
 * the test read the generated class instead.
 */
final class CapturingHookConvertRector extends HookConvertRector
{
    public function __destruct()
    {
        $this->module = '';
    }

    /**
     * Forgets the previously captured hook class.
     *
     * The hook class is only rebuilt when the rule sees a different file, and
     * Rector's test container shares this instance between tests, so a second
     * test on the same fixture would otherwise append to the first one's class.
     */
    public function reset(): void
    {
        $this->inputFilename = '';
        $this->useStmts = [];
        $this->needsTranslationTrait = false;
    }

    /**
     * Call once per run: adding the translation trait is not idempotent.
     */
    public function printHookClass(BetterStandardPrinter $printer): string
    {
        return $printer->prettyPrintFile($this->buildHookClassStmts());
    }
}
