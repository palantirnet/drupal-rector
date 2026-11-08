<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub;

use DrupalRector\Rector\Convert\HookConvertRector;
use Rector\PhpParser\Printer\BetterStandardPrinter;

/**
 * Keeps the generated hook class in memory instead of writing it to disk.
 */
final class CapturingHookConvertRector extends HookConvertRector
{
    public function __destruct()
    {
        $this->module = '';
    }

    public function printHookClass(BetterStandardPrinter $printer): string
    {
        return $printer->prettyPrintFile($this->buildHookClassStmts());
    }
}
