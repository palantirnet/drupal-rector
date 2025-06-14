<?php

declare(strict_types=1);

namespace Drupal9\Rector\Deprecation\PassRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class PassRectorTest extends AbstractRectorTestCase
{
    /**
     * @covers ::refactor
     *
     * @dataProvider provideData
     */
    public function test(string $filePath): void
    {
        if (str_contains($filePath, 'skip') && method_exists($this, 'doTestFileExpectingWarningAboutRuleApplied')) {
            $this->doTestFileExpectingWarningAboutRuleApplied($filePath, 'DrupalRector\Drupal9\Rector\Deprecation\PassRector');

            return;
        }

        $this->doTestFile($filePath);
    }

    /**
     * @return Iterator<<string>>
     */
    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture');
    }

    public function provideConfigFilePath(): string
    {
        // must be implemented
        return __DIR__.'/config/configured_rule.php';
    }
}
