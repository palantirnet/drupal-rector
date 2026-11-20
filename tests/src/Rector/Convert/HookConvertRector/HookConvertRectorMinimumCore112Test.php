<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\RecordingHookConvertRector;

/**
 * Core only registers theme hook classes from 11.3.0, so with backwards
 * compatibility on, a theme whose minimum is 11.2.0 is still left alone.
 */
class HookConvertRectorMinimumCore112Test extends AbstractDrupalRectorTestCase
{
    private const FIXTURES = __DIR__.'/fixture-skip-legacy';

    protected function setUp(): void
    {
        parent::setUp();
        // Drop anything a previous test left in the shared rule.
        $this->flush();
        // The settings object is shared with the rule; tearDown() restores the defaults.
        $this->make(DrupalRectorSettings::class)->setMinimumCoreVersionSupported('11.2.0');
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
