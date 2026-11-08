<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\CapturingHookConvertRector;
use Rector\PhpParser\Printer\BetterStandardPrinter;

/**
 * With importNames(), Rector 2.8 re-runs rules on the changed procedural file,
 * which then carries the imports for the LegacyHook wrappers. Those must not
 * be copied into the hook class, where the self-import is a fatal error.
 */
class HookConvertRectorImportNamesTest extends AbstractDrupalRectorTestCase
{
    public function test(): void
    {
        $this->doTestFile(__DIR__.'/fixture-import-names/hookconvertrector/import_names.module.inc');

        $hookClass = $this->make(CapturingHookConvertRector::class)->printHookClass($this->make(BetterStandardPrinter::class));

        $this->assertStringContainsString('class ImportNamesHooks', $hookClass);
        $this->assertStringContainsString('use Drupal\Core\Url;', $hookClass);
        $this->assertStringNotContainsString('use Drupal\hookconvertrector\Hook\ImportNamesHooks;', $hookClass);
        $this->assertStringNotContainsString('use Drupal\Core\Hook\Attribute\LegacyHook;', $hookClass);
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/import_names_rule.php';
    }
}
