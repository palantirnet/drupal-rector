<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Set\UpstreamPhpPhpunitSet;

use DrupalRector\Tests\AbstractDrupalRectorTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Rector\Configuration\Option;
use Rector\Configuration\Parameter\SimpleParameterProvider;
use Rector\ValueObject\PhpVersion;

/**
 * The upstream rules are opt-in: none of them runs unless
 * DrupalSetList::UPSTREAM_PHP_PHPUNIT is loaded.
 */
final class NotLoadedByComposerBasedOnlyTest extends AbstractDrupalRectorTestCase
{
    /**
     * Set once for the class: Rector loads the config on the first test only,
     * and the version has to be in place by then, not reset before every test.
     */
    public static function setUpBeforeClass(): void
    {
        SimpleParameterProvider::setParameter(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_84);

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
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-not-loaded', 'set_not_loaded*.php.inc');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/composer_based_only.php';
    }
}
