<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub;

use DrupalRector\Rector\Convert\HookConvertRector;

/**
 * Records the files the rule would write instead of writing them to disk.
 *
 * Unlike the other stubs it keeps the real __destruct() logic, so the decision
 * about the hook class and the services.yml entry is the production one.
 */
final class RecordingHookConvertRector extends HookConvertRector
{
    /**
     * @var array<string, string>
     */
    public array $written = [];

    protected function writeFile(string $fileName, string $contents): void
    {
        $this->written[$fileName] = $contents;
    }

    /**
     * Flushes the hook class of the last processed file, as Rector does when it
     * drops the rule, and returns what was written keyed by base name.
     *
     * @return array<string, string>
     */
    public function flush(): array
    {
        $this->__destruct();
        // Make the next file start from scratch, as a new Rector run would.
        $this->inputFilename = '';
        $written = [];
        foreach ($this->written as $path => $contents) {
            $written[basename($path)] = $contents;
        }
        $this->written = [];

        return $written;
    }
}
