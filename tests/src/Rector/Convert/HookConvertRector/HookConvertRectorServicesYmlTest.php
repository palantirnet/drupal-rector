<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\RecordingHookConvertRector;

/**
 * The LegacyHook wrapper needs a services.yml entry. When the module already
 * has a services.yml the entry is appended to it, once.
 */
class HookConvertRectorServicesYmlTest extends AbstractDrupalRectorTestCase
{
    private const FIXTURES = __DIR__.'/fixture-services-yml';

    protected function setUp(): void
    {
        parent::setUp();
        // Drop anything a previous test left in the shared rule.
        $this->flush();
    }

    public function testEntryIsAppendedToExistingServicesYml(): void
    {
        $this->doTestFile(self::FIXTURES.'/append/append.module.inc');

        $written = $this->flush();
        $this->assertSame(['AppendHooks.php', 'servicesyml.services.yml'], array_keys($written));
        $existing = (string) file_get_contents(self::FIXTURES.'/append/servicesyml.services.yml');
        $this->assertSame(
            $existing."\n  Drupal\\servicesyml\\Hook\\AppendHooks:\n    class: Drupal\\servicesyml\\Hook\\AppendHooks\n    autowire: true\n",
            $written['servicesyml.services.yml'],
        );
        $this->assertSame(1, substr_count($written['servicesyml.services.yml'], 'services:'));
    }

    public function testAlreadyRegisteredEntryIsNotDuplicated(): void
    {
        $this->doTestFile(self::FIXTURES.'/registered/append.module.inc');

        $written = $this->flush();
        $this->assertSame(['AppendHooks.php'], array_keys($written));
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
