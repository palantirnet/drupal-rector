<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\RecordingHookConvertRector;

/**
 * A hook class from an earlier run must not be overwritten: the next one gets a
 * numeric suffix, and the wrapper and services.yml entry use that name.
 */
class HookConvertRectorClassCounterTest extends AbstractDrupalRectorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Drop anything a previous test left in the shared rule.
        $this->flush();
    }

    public function testExistingHookClassGetsNumericSuffix(): void
    {
        // The fixture already contains src/Hook/CounterHooks.php. The expected
        // output of the fixture file names CounterHooks1 in the wrapper.
        $this->doTestFile(__DIR__.'/fixture-class-counter/counter.module.inc');

        $written = $this->flush();
        $this->assertSame(['CounterHooks1.php', 'classcounter.services.yml'], array_keys($written));
        $this->assertStringContainsString('class CounterHooks1', $written['CounterHooks1.php']);
        $this->assertStringContainsString('  Drupal\\classcounter\\Hook\\CounterHooks1:', $written['classcounter.services.yml']);
        $this->assertStringContainsString('    class: Drupal\\classcounter\\Hook\\CounterHooks1', $written['classcounter.services.yml']);
        $this->assertStringNotContainsString('CounterHooks:', $written['classcounter.services.yml']);
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
