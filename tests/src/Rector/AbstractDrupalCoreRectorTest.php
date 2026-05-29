<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Services\DrupalRectorSettings;
use PhpParser\Node;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AbstractDrupalCoreRectorTest extends TestCase
{
    #[DataProvider('provideSupportBackwardsCompatibility')]
    public function testSupportBackwardsCompatibility(
        bool $bcEnabled,
        string $minimumVersion,
        string $introducedVersion,
        bool $expected,
    ): void {
        $settings = new DrupalRectorSettings();
        $bcEnabled ? $settings->enableBackwardCompatibility() : $settings->disableBackwardCompatibility();
        $settings->setMinimumCoreVersionSupported($minimumVersion);

        $rector = self::makeRector($settings);
        $configuration = self::makeConfiguration($introducedVersion);

        self::assertSame($expected, $rector->supportBackwardsCompatibility($configuration));
    }

    /**
     * @return \Iterator<string, array{bool, string, string, bool}>
     */
    public static function provideSupportBackwardsCompatibility(): \Iterator
    {
        // BC disabled wins regardless of versions.
        yield 'bc disabled returns false' => [false, '10.1.0', '10.2.0', false];

        // Minimum supported version below the BC-eligible floor (10.1.0).
        yield 'minimum 10.0.0 below floor' => [true, '10.0.0', '10.2.0', false];
        yield 'minimum 9.5.0 below floor' => [true, '9.5.0', '10.2.0', false];

        // Introduced version below 10.0.0 — no BC wrappers for pre-10 deprecations.
        yield 'introduced 9.5.0 too old' => [true, '10.1.0', '9.5.0', false];

        // Project minimum already covers the introduced version — wrap unnecessary.
        yield 'minimum equal to introduced' => [true, '10.2.0', '10.2.0', false];
        yield 'minimum above introduced' => [true, '11.0.0', '10.2.0', false];
        yield 'minimum 10.1.0 equal to introduced floor' => [true, '10.1.0', '10.1.0', false];

        // Happy paths.
        yield 'minimum 10.1.0 introduced 10.2.0' => [true, '10.1.0', '10.2.0', true];
        yield 'minimum 10.1.0 introduced 11.4.0' => [true, '10.1.0', '11.4.0', true];
        yield 'minimum 10.5.0 introduced 11.2.0' => [true, '10.5.0', '11.2.0', true];
    }

    private static function makeRector(DrupalRectorSettings $settings): AbstractDrupalCoreRector
    {
        return new class($settings) extends AbstractDrupalCoreRector {
            public function getNodeTypes(): array
            {
                return [];
            }

            public function getRuleDefinition(): RuleDefinition
            {
                return new RuleDefinition('test', []);
            }

            protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration)
            {
                return null;
            }
        };
    }

    private static function makeConfiguration(string $introducedVersion): VersionedConfigurationInterface
    {
        return new class($introducedVersion) implements VersionedConfigurationInterface {
            public function __construct(private readonly string $introducedVersion)
            {
            }

            public function getIntroducedVersion(): string
            {
                return $this->introducedVersion;
            }
        };
    }
}
