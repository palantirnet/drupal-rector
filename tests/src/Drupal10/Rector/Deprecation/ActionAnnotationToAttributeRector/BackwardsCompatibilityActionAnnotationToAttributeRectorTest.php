<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal10\Rector\Deprecation\ActionAnnotationToAttributeRector;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class Drupal
{
    public const VERSION = '11.0.x-dev';
}

class BackwardsCompatibilityActionAnnotationToAttributeRectorTest extends AbstractRectorTestCase
{
    /**
     * @covers ::refactor
     *
     * @dataProvider provideData
     */
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    /**
     * @return Iterator<<string>>
     */
    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-next-major');
    }

    public function provideConfigFilePath(): string
    {
        // must be implemented
        return __DIR__.'/config/configured_rule_simulate_next_major.php';
    }
}
