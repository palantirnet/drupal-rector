<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\PHPUnit\PhpUnitTestAnnotationToAttributeRector;

use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use Iterator;

#[\PHPUnit\Framework\Attributes\CoversFunction('refactor')]
class BackwardCompatibilityDisabledPhpUnitTestAnnotationToAttributeRectorTest extends AbstractDrupalRectorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // The Rector container is shared and tearDown() re-enables BC after each
        // test. Re-disable it here so every test in this class runs with BC off.
        static::getContainer()->make(DrupalRectorSettings::class)->disableBackwardCompatibility();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideData')]
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
        return __DIR__.'/config/configured_rule_bc_disabled.php';
    }
}
