<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Set\UpstreamPhpPhpunitSet;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Configuration\Option;
use Rector\Configuration\Parameter\SimpleParameterProvider;
use Rector\ValueObject\PhpVersion;

/**
 * Runs the opt-in set against a project whose composer.json allows PHP 8.1.
 *
 * Rector's test case defaults the target PHP version to the newest one, which
 * would hide a rule that is gated on PHP 8.4. Pinning it to 8.1 first makes the
 * fixtures prove that the set raises the target itself.
 */
final class UpstreamPhpPhpunitSetTest extends AbstractDrupalRectorTestCase
{
    /**
     * Set once for the class: Rector loads the config on the first test only,
     * and the version has to be in place by then, not reset before every test.
     */
    public static function setUpBeforeClass(): void
    {
        SimpleParameterProvider::setParameter(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_81);

        parent::setUpBeforeClass();
    }

    #[DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    /**
     * @return \Iterator<string, array<string>>
     */
    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/set.php';
    }
}
