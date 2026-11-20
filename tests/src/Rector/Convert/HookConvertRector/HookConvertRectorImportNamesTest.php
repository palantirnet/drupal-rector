<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\HookClassAssertions;

/**
 * With importNames(), Rector 2.8 re-runs rules on the changed procedural file,
 * which then carries the imports for the LegacyHook wrappers. Those must not
 * be copied into the hook class, where the self-import is a fatal error.
 */
class HookConvertRectorImportNamesTest extends AbstractDrupalRectorTestCase
{
    use HookClassAssertions;

    public function test(): void
    {
        $this->doTestFileAndHookClass(
            __DIR__.'/fixture-import-names/hookconvertrector/import_names.module.inc',
            __DIR__.'/fixture-import-names/expected/import_names.php.inc',
        );
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/import_names_rule.php';
    }
}
