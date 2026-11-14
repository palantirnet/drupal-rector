<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\RecordingHookConvertRector;

/**
 * From a minimum of 11.1.0 core discovers hook classes itself, so a module needs
 * neither the LegacyHook wrapper nor a services.yml entry.
 */
class HookConvertRectorMinimumCore111Test extends AbstractDrupalRectorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Drop anything a previous test left in the shared rule.
        $this->flush();
        // The settings object is shared with the rule; tearDown() restores the defaults.
        $this->make(DrupalRectorSettings::class)->setMinimumCoreVersionSupported('11.1.0');
    }

    public function testModuleHasNoWrapperAndNoServicesYml(): void
    {
        $this->doTestFile(__DIR__.'/fixture-skip-legacy/module-min-11-1/user_cancel.module.inc');

        $written = $this->flush();
        $this->assertSame(['UserCancelHooks.php'], array_keys($written));
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
