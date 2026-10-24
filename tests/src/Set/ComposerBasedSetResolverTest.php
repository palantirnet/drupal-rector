<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Set;

use DrupalRector\Set\ComposerBasedSetResolver;
use DrupalRector\Set\DrupalSetList;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ComposerBasedSetResolverTest extends TestCase
{
    /**
     * @return string[]
     */
    private function resolve(?string $installedCoreVersion): array
    {
        return (new ComposerBasedSetResolver())->resolve($installedCoreVersion);
    }

    public function testComposerBasedSetFileExists(): void
    {
        self::assertFileExists(DrupalSetList::COMPOSER_BASED);
    }

    public function testEveryResolvedSetFileExists(): void
    {
        $sets = $this->resolve('11.4.0.0');
        self::assertNotEmpty($sets);

        foreach ($sets as $set) {
            self::assertFileExists($set);
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
            '11.4.0 loads 11.0-11.4 cumulatively' => ['11.4.0.0', [
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
            '11.3.0 loads 11.0-11.3, not 11.4' => ['11.3.0.0', [
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
            '11.0.0 loads only 11.0' => ['11.0.0.0', [
                'drupal-11.0-deprecations.php',
                'drupal-bootstrap.php',
            ]],
            // Drupal 10 gets only 10.x deprecations + bootstrap; no breaking sets exist.
            '10.3.0 loads 10.0-10.3, no 11.x' => ['10.3.0.0', [
                'drupal-10.0-deprecations.php',
                'drupal-10.1-deprecations.php',
                'drupal-10.2-deprecations.php',
                'drupal-10.3-deprecations.php',
                'drupal-bootstrap.php',
            ]],
            // Dev core matches its minor, in both the raw and normalized form.
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
            'normalized 11.4.x-dev matches like 11.4' => ['11.4.9999999.9999999-dev', [
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
            // The next major's floor loads only its own set + bootstrap.
            '12.0.0 loads only 12.0' => ['12.0.0.0', [
                'drupal-12.0-deprecations.php',
                'drupal-bootstrap.php',
            ]],
            // Unsupported majors and dev branches without a version load nothing.
            '9.5.0 loads nothing' => ['9.5.0.0', []],
            'dev-main loads nothing' => ['dev-main', []],
        ];
    }

    /**
     * @param string[] $expectedFiles
     */
    #[DataProvider('installedVersionProvider')]
    public function testCumulativeMatchingByInstalledCoreVersion(string $installedCoreVersion, array $expectedFiles): void
    {
        $matched = array_map(basename(...), $this->resolve($installedCoreVersion));

        sort($matched);
        $expected = $expectedFiles;
        sort($expected);

        self::assertSame($expected, $matched);
    }

    public function testNoMatchWhenDrupalCoreAbsent(): void
    {
        self::assertSame([], $this->resolve(null));
    }
}
