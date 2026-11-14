<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\RecordingHookConvertRector;

/**
 * With the default settings (backwards compatibility on, minimum core 10.1.0)
 * modules keep the LegacyHook wrapper plus services.yml entry and themes are
 * left alone. Each file is decided on its own: a skipped theme must not change
 * what happens to a module processed after it.
 */
class HookConvertRectorDefaultSettingsTest extends AbstractDrupalRectorTestCase
{
    private const FIXTURES = __DIR__.'/fixture-skip-legacy';

    protected function setUp(): void
    {
        parent::setUp();
        // Drop anything a previous test left in the shared rule.
        $this->flush();
    }

    public function testModuleKeepsLegacyWrapperAndServicesYml(): void
    {
        $this->doTestFile(__DIR__.'/fixture/hookconvertrector/no_return.module.inc');

        $written = $this->flush();
        $this->assertSame(['NoReturnHooks.php', 'hookconvertrector.services.yml'], array_keys($written));
        $this->assertStringContainsString('Drupal\\hookconvertrector\\Hook\\NoReturnHooks:', $written['hookconvertrector.services.yml']);
    }

    public function testThemeIsLeftUntouched(): void
    {
        $this->doTestFile(self::FIXTURES.'/theme-defaults/skiplegacytheme.theme.inc');

        $this->assertSame([], $this->flush());
    }

    public function testIncFileOfThemeIsLeftUntouched(): void
    {
        $this->doTestFile(self::FIXTURES.'/theme-defaults/theme_helpers.inc.inc');

        $this->assertSame([], $this->flush());
    }

    public function testSkippedThemeDoesNotAffectModuleProcessedAfterIt(): void
    {
        $this->doTestFile(self::FIXTURES.'/theme-defaults/skiplegacytheme.theme.inc');
        $themeInput = self::FIXTURES.'/theme-defaults/skiplegacytheme.theme';
        try {
            $this->doTestFile(__DIR__.'/fixture/hookconvertrector/no_return.module.inc');
        } finally {
            // The test case only removes the input file of the last doTestFile().
            @unlink($themeInput);
        }

        $written = $this->flush();
        $this->assertSame(['NoReturnHooks.php', 'hookconvertrector.services.yml'], array_keys($written));
    }

    public function testSkippedThemeDoesNotChangeDecisionForModuleProcessedBeforeIt(): void
    {
        $this->doTestFile(__DIR__.'/fixture/hookconvertrector/no_return.module.inc');
        $moduleInput = __DIR__.'/fixture/hookconvertrector/no_return.module';
        try {
            $this->doTestFile(self::FIXTURES.'/theme-defaults/skiplegacytheme.theme.inc');
        } finally {
            @unlink($moduleInput);
        }

        // The module's hook class is flushed when the theme file is seen. Its
        // services.yml entry must follow the module's own decision, not the
        // theme's.
        $written = $this->flush();
        $this->assertSame(['NoReturnHooks.php', 'hookconvertrector.services.yml'], array_keys($written));
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/recording_rule.php';
    }

    /**
     * @return array<string, string>
     */
    private function flush(): array
    {
        return $this->make(RecordingHookConvertRector::class)->flush();
    }
}
