<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\HookClassAssertions;

/**
 * A single run together with importNames(): the hook class gets the imports
 * its body needs, but neither the LegacyHook import nor an import of itself,
 * which only the procedural wrappers use.
 */
class HookConvertRectorSingleRunImportNamesTest extends AbstractDrupalRectorTestCase
{
    use HookClassAssertions;

    public function test(): void
    {
        $this->doTestFileAndHookClass(
            __DIR__.'/fixture-single-run-import-names/singlerun/single_run_import_names.module.inc',
            __DIR__.'/fixture-single-run-import-names/expected/single_run_import_names.php.inc',
        );
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/single_run_import_names.php';
    }
}
