<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\RecordingHookConvertRector;

/**
 * Without backwards compatibility there is no reason for a wrapper: the
 * procedural function is removed and no services.yml entry is written, for
 * modules and for themes.
 */
class HookConvertRectorBackwardCompatibilityOffTest extends AbstractDrupalRectorTestCase
{
    private const FIXTURES = __DIR__.'/fixture-skip-legacy';

    protected function setUp(): void
    {
        parent::setUp();
        // Drop anything a previous test left in the shared rule.
        $this->flush();
        // The settings object is shared with the rule; tearDown() restores the defaults.
        $this->make(DrupalRectorSettings::class)->disableBackwardCompatibility();
    }

    public function testModuleHasNoWrapperAndNoServicesYml(): void
    {
        $this->doTestFile(self::FIXTURES.'/module-bc-off/user_cancel.module.inc');

        $written = $this->flush();
        $this->assertSame(['UserCancelHooks.php'], array_keys($written));
        $this->assertStringContainsString('#[Hook(\'user_cancel\')]', $written['UserCancelHooks.php']);
    }

    public function testThemeIsConvertedWithoutWrapper(): void
    {
        $this->doTestFile(self::FIXTURES.'/theme-bc-off/skiplegacytheme.theme.inc');

        $written = $this->flush();
        $this->assertSame(['SkiplegacythemeHooks.php'], array_keys($written));
        $this->assertStringContainsString('#[Hook(\'preprocess_page\')]', $written['SkiplegacythemeHooks.php']);
    }

    public function testIncFileOfThemeIsConvertedWithoutWrapper(): void
    {
        $this->doTestFile(self::FIXTURES.'/theme-bc-off/theme_helpers.inc.inc');

        $this->assertSame(['ThemeHelpersHooks.php'], array_keys($this->flush()));
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
