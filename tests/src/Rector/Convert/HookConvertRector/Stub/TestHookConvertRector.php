<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub;

use DrupalRector\Rector\Convert\HookConvertRector;

/**
 * Prevents file-system side effects during testing.
 *
 * HookConvertRector::__destruct() writes src/Hook/<Class>.php and a services
 * YAML file. Those writes are irrelevant to the transformation under test and
 * would pollute the fixture directory, so this subclass skips them.
 */
final class TestHookConvertRector extends HookConvertRector
{
    public function __destruct()
    {
        $this->module = '';
    }
}
