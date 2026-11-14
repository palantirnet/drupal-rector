<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\RecordingHookConvertRector;

/**
 * From a minimum of 11.3.0 core registers theme hook classes, so a theme is
 * converted. A theme never gets a wrapper or a services.yml entry.
 */
class HookConvertRectorMinimumCore113Test extends AbstractDrupalRectorTestCase
{
    private const FIXTURES = __DIR__.'/fixture-skip-legacy';

    protected function setUp(): void
    {
        parent::setUp();
        // Drop anything a previous test left in the shared rule.
        $this->flush();
        // The settings object is shared with the rule; tearDown() restores the defaults.
        $this->make(DrupalRectorSettings::class)->setMinimumCoreVersionSupported('11.3.0');
    }

    public function testThemeIsConvertedWithoutWrapper(): void
    {
        $this->doTestFile(self::FIXTURES.'/theme-min-11-3/skiplegacytheme.theme.inc');

        $written = $this->flush();
        $this->assertSame(['SkiplegacythemeHooks.php'], array_keys($written));
    }

    public function testIncFileOfThemeIsConvertedWithoutWrapper(): void
    {
        $this->doTestFile(self::FIXTURES.'/theme-min-11-3/theme_helpers.inc.inc');

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
