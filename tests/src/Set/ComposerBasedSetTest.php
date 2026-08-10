<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Set;

use DrupalRector\Set\DrupalSetList;
use PHPUnit\Framework\TestCase;

final class ComposerBasedSetTest extends TestCase
{
    private const ROOT_DIR = __DIR__.'/../../..';

    public function testSetListConstantPointsAtTheGeneratedFile(): void
    {
        self::assertFileExists(DrupalSetList::COMPOSER_BASED);
        self::assertSame(
            realpath(self::ROOT_DIR.'/config/composer-based.php'),
            realpath(DrupalSetList::COMPOSER_BASED)
        );
    }

    /**
     * The committed file is generated; a rule added to a per-minor config
     * without re-running the generator would silently never be version-bound.
     */
    public function testIsUpToDateWithTheGenerator(): void
    {
        $temporaryFilePath = (string) tempnam(sys_get_temp_dir(), 'composer-based-');

        $command = sprintf(
            '%s %s %s',
            escapeshellarg(PHP_BINARY),
            escapeshellarg(self::ROOT_DIR.'/scripts/generate-composer-based.php'),
            escapeshellarg($temporaryFilePath)
        );

        exec($command, $output, $exitCode);
        self::assertSame(0, $exitCode, implode("\n", $output));

        $generated = (string) file_get_contents($temporaryFilePath);
        unlink($temporaryFilePath);

        self::assertSame(
            $this->readComposerBasedSet(),
            $generated,
            'config/composer-based.php is out of date, run `php scripts/generate-composer-based.php`'
        );
    }

    public function testEveryRegistrationIsBoundToAnExactCoreVersion(): void
    {
        $contents = $this->readComposerBasedSet();

        preg_match_all('#\$ruleSince\((\S+)::class, (\S+)\);#', $contents, $ruleSinceMatches, PREG_SET_ORDER);
        preg_match_all(
            '#ruleWithConfigurationComposerVersionBound\((\S+)::class,.*?\], (\S+), (\S+)\);#s',
            $contents,
            $boundMatches,
            PREG_SET_ORDER
        );

        self::assertNotEmpty($ruleSinceMatches);
        self::assertNotEmpty($boundMatches);

        foreach ($ruleSinceMatches as $match) {
            self::assertMatchesRegularExpression("#^'>=\d+\.\d+\.\d+'$#", $match[2], $match[1]);
        }

        foreach ($boundMatches as $match) {
            self::assertSame("'drupal/core'", $match[2], $match[1]);
            self::assertMatchesRegularExpression("#^'>=\d+\.\d+\.\d+'$#", $match[3], $match[1]);
        }
    }

    /**
     * Every rule the per-minor sets register must also be registered here,
     * otherwise composer-based selection silently covers less.
     */
    public function testCoversEveryRuleOfThePerMinorSets(): void
    {
        $composerBasedContents = $this->readComposerBasedSet();

        $missingRectorClasses = [];

        foreach ($this->providePerMinorConfigFilePaths() as $configFilePath) {
            $configContents = (string) file_get_contents($configFilePath);

            preg_match_all('#(?:rule|ruleWithConfiguration)\((\w+)::class#', $configContents, $matches);

            foreach (array_unique($matches[1]) as $shortClassName) {
                if (!str_ends_with($shortClassName, 'Rector')) {
                    continue;
                }

                if (str_contains($composerBasedContents, $shortClassName.'::class')) {
                    continue;
                }

                $missingRectorClasses[] = basename($configFilePath).': '.$shortClassName;
            }
        }

        self::assertSame([], $missingRectorClasses);
    }

    /**
     * @return string[]
     */
    private function providePerMinorConfigFilePaths(): array
    {
        return array_values(array_filter(
            (array) glob(self::ROOT_DIR.'/config/drupal-*/drupal-*.php'),
            static fn (string $filePath): bool => preg_match(
                '#^drupal-\d+\.\d+-(deprecations|breaking)\.php$#',
                basename($filePath)
            ) === 1
        ));
    }

    private function readComposerBasedSet(): string
    {
        return (string) file_get_contents(self::ROOT_DIR.'/config/composer-based.php');
    }
}
