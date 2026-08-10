<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Set;

use DrupalRector\Set\DrupalSetList;
use PHPUnit\Framework\TestCase;
use Rector\VersionBonding\Contract\ComposerPackageConstraintInterface;

final class ComposerBasedSetTest extends TestCase
{
    private const ROOT_DIR = __DIR__.'/../../..';

    public function testSetListConstantPointsAtTheSetFile(): void
    {
        self::assertFileExists(DrupalSetList::COMPOSER_BASED);
        self::assertSame(
            realpath(self::ROOT_DIR.'/config/composer-based.php'),
            realpath(DrupalSetList::COMPOSER_BASED)
        );
    }

    public function testEveryConfiguredRuleIsBoundToAnExactCoreVersion(): void
    {
        preg_match_all(
            '#ruleWithConfigurationComposerVersionBound\((\S+)::class,.*?\], (\S+), (\S+)\);#s',
            $this->readComposerBasedSet(),
            $matches,
            PREG_SET_ORDER
        );

        self::assertNotEmpty($matches);

        foreach ($matches as $match) {
            self::assertSame("'drupal/core'", $match[2], $match[1]);
            self::assertMatchesRegularExpression("#^'>=\d+\.\d+\.\d+'$#", $match[3], $match[1]);
        }
    }

    /**
     * A rule that takes no configuration cannot state its version in the set, so
     * it has to declare it on the class instead — otherwise it would run on every
     * Drupal version.
     */
    public function testEveryPlainlyRegisteredRuleDeclaresItsCoreConstraint(): void
    {
        $contents = $this->readComposerBasedSet();

        preg_match_all('#^use (\S+\\\\(\w+));$#m', $contents, $importMatches, PREG_SET_ORDER);
        $importedClassNames = array_column($importMatches, 1, 2);

        preg_match_all('#\$rectorConfig->rule\((\S+)::class\);#', $contents, $ruleMatches);
        self::assertNotEmpty($ruleMatches[1]);

        foreach (array_unique($ruleMatches[1]) as $shortClassName) {
            $rectorClass = $importedClassNames[$shortClassName] ?? ltrim($shortClassName, '\\');

            self::assertTrue(
                is_a($rectorClass, ComposerPackageConstraintInterface::class, true),
                $rectorClass.' must implement '.ComposerPackageConstraintInterface::class
            );

            // some rules take constructor dependencies, which the constraint does not use
            $rector = new \ReflectionClass($rectorClass)->newInstanceWithoutConstructor();
            self::assertInstanceOf(ComposerPackageConstraintInterface::class, $rector);

            $composerPackageConstraint = $rector->provideComposerPackageConstraint();

            self::assertSame('drupal/core', $composerPackageConstraint->getPackageName(), $rectorClass);
            self::assertMatchesRegularExpression(
                '#^>=\d+\.\d+\.\d+$#',
                $composerPackageConstraint->getConstraint(),
                $rectorClass
            );
        }
    }

    /**
     * The registrations are duplicated between here and the per-minor configs on
     * purpose, so this is the guard against the two drifting apart: every rule
     * the per-minor sets register must also be registered here, otherwise
     * composer-based selection silently covers less.
     */
    public function testCoversEveryRuleOfThePerMinorSets(): void
    {
        $composerBasedContents = $this->readComposerBasedSet();

        $missingRectorClasses = [];

        foreach ($this->providePerMinorConfigFilePaths() as $configFilePath) {
            $configContents = (string) file_get_contents($configFilePath);

            preg_match_all('#^use (\S+\\\\(\w+));$#m', $configContents, $importMatches, PREG_SET_ORDER);
            $importedClassNames = array_column($importMatches, 1, 2);

            // rule(), ruleWithConfiguration() and the entries of a rules([]) call
            preg_match_all('#(\w+)::class#', $configContents, $matches);

            foreach (array_unique($matches[1]) as $shortClassName) {
                if (!str_ends_with($shortClassName, 'Rector')) {
                    continue;
                }

                // rules of the other Rector packages are bound by their own composer-based set
                if (!str_starts_with($importedClassNames[$shortClassName] ?? '', 'DrupalRector\\')) {
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
