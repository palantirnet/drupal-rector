<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Set;

use DrupalRector\Set\DrupalSetProvider;
use PHPUnit\Framework\TestCase;
use Rector\Composer\ValueObject\InstalledPackage;
use Rector\Set\ValueObject\ComposerTriggeredSet;

final class DrupalSetProviderTest extends TestCase
{
    /**
     * @return ComposerTriggeredSet[]
     */
    private function provide(): array
    {
        return (new DrupalSetProvider())->provide();
    }

    public function testEverySetIsADrupalCoreComposerTriggeredSet(): void
    {
        $sets = $this->provide();
        self::assertNotEmpty($sets);

        foreach ($sets as $set) {
            self::assertInstanceOf(ComposerTriggeredSet::class, $set);
            self::assertSame('drupal', $set->getGroupName());
            // getName() is "<package> <version>".
            self::assertStringStartsWith('drupal/core ', $set->getName());
            self::assertFileExists($set->getSetFilePath());
        }
    }

    /**
     * @return array<string, array{string, string[]}>
     */
    public static function installedVersionProvider(): array
    {
        return [
            // A site on 11.4 gets every 11.x set up to 11.4 (deprecations +
            // breaking) plus the bootstrap, and nothing from Drupal 10.
            '11.4.0 loads 11.0-11.4 cumulatively' => ['11.4.0', [
                'drupal-11.0-deprecations.php',
                'drupal-11.1-deprecations.php',
                'drupal-11.2-deprecations.php',
                'drupal-11.3-deprecations.php',
                'drupal-11.4-deprecations.php',
                'drupal-11.1-breaking.php',
                'drupal-11.2-breaking.php',
                'drupal-11.3-breaking.php',
                'drupal-11.4-breaking.php',
                'drupal-bootstrap.php',
            ]],
            // A mid-range minor stops at its own version — 11.4 must not leak in.
            '11.3.0 loads 11.0-11.3, not 11.4' => ['11.3.0', [
                'drupal-11.0-deprecations.php',
                'drupal-11.1-deprecations.php',
                'drupal-11.2-deprecations.php',
                'drupal-11.3-deprecations.php',
                'drupal-11.1-breaking.php',
                'drupal-11.2-breaking.php',
                'drupal-11.3-breaking.php',
                'drupal-bootstrap.php',
            ]],
            // The major floor loads only its own set + bootstrap.
            '11.0.0 loads only 11.0' => ['11.0.0', [
                'drupal-11.0-deprecations.php',
                'drupal-bootstrap.php',
            ]],
            // Drupal 10 gets only 10.x deprecations + bootstrap; no breaking sets exist.
            '10.3.0 loads 10.0-10.3, no 11.x' => ['10.3.0', [
                'drupal-10.0-deprecations.php',
                'drupal-10.1-deprecations.php',
                'drupal-10.2-deprecations.php',
                'drupal-10.3-deprecations.php',
                'drupal-bootstrap.php',
            ]],
            // Pre-release / dev core still matches (composer/semver is lenient).
            '11.4.x-dev matches like 11.4' => ['11.4.x-dev', [
                'drupal-11.0-deprecations.php',
                'drupal-11.1-deprecations.php',
                'drupal-11.2-deprecations.php',
                'drupal-11.3-deprecations.php',
                'drupal-11.4-deprecations.php',
                'drupal-11.1-breaking.php',
                'drupal-11.2-breaking.php',
                'drupal-11.3-breaking.php',
                'drupal-11.4-breaking.php',
                'drupal-bootstrap.php',
            ]],
            // A future major out of range matches nothing.
            '12.0.0 matches nothing' => ['12.0.0', []],
        ];
    }

    /**
     * @param string[] $expectedFiles
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('installedVersionProvider')]
    public function testCumulativeMatchingByInstalledCoreVersion(string $installedVersion, array $expectedFiles): void
    {
        $installedPackages = [
            'drupal/core' => new InstalledPackage('drupal/core', $installedVersion),
        ];

        $matched = [];
        foreach ($this->provide() as $set) {
            if ($set->matchInstalledPackages($installedPackages)) {
                $matched[] = basename($set->getSetFilePath());
            }
        }

        sort($matched);
        $expected = $expectedFiles;
        sort($expected);

        self::assertSame($expected, $matched);
    }

    public function testNoMatchWhenDrupalCoreAbsent(): void
    {
        foreach ($this->provide() as $set) {
            self::assertFalse($set->matchInstalledPackages([]));
        }
    }
}
