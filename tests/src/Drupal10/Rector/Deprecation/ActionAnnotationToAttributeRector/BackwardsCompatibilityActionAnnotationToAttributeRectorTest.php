<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal10\Rector\Deprecation\ActionAnnotationToAttributeRector;

use DrupalRector\Rector\AbstractDrupalCoreRector;
use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class BackwardsCompatibilityActionAnnotationToAttributeRectorTest extends AbstractRectorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        AbstractDrupalCoreRector::setVersionOverride('11.0.x-dev');
    }

    protected function tearDown(): void
    {
        AbstractDrupalCoreRector::setVersionOverride(null);
        parent::tearDown();
    }

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
