<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub;

use Rector\PhpParser\Printer\BetterStandardPrinter;

/**
 * For rector test cases using CapturingHookConvertRector.
 *
 * @mixin \DrupalRector\Tests\AbstractDrupalRectorTestCase
 */
trait HookClassAssertions
{
    /**
     * Runs the fixture (checking the procedural output) and then compares the
     * generated hook class exactly with the expected file.
     *
     * Only a trailing newline is ignored.
     */
    protected function doTestFileAndHookClass(string $fixturePath, string $expectedHookClassPath): void
    {
        $rector = $this->make(CapturingHookConvertRector::class);
        $rector->reset();

        $this->doTestFile($fixturePath);

        $expected = file_get_contents($expectedHookClassPath);
        $this->assertIsString($expected, "Cannot read $expectedHookClassPath");
        $actual = $rector->printHookClass($this->make(BetterStandardPrinter::class));

        $this->assertSame(rtrim($expected, "\n"), rtrim($actual, "\n"), "Generated hook class differs from $expectedHookClassPath");
    }
}
