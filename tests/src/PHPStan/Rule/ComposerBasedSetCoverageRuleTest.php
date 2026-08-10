<?php

declare(strict_types=1);

namespace DrupalRector\Tests\PHPStan\Rule;

use DrupalRector\PHPStan\Collector\RegisteredRectorClassCollector;
use DrupalRector\PHPStan\Rule\ComposerBasedSetCoverageRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ComposerBasedSetCoverageRule>
 */
final class ComposerBasedSetCoverageRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse(
            [
                __DIR__.'/Fixture/Coverage/Set/composer-based.php',
                __DIR__.'/Fixture/Coverage/PerMinor/drupal-11.3-deprecations.php',
            ],
            [
                [
                    '"DrupalRector\Drupal11\Rector\Deprecation\ErrorCurrentErrorHandlerRector" is missing from the composer-based set, so it is never applied by composer-based selection. Register it in config/composer-based.php too.',
                    1,
                ],
            ]
        );
    }

    /**
     * Without the set in the analysed files there is nothing to compare against,
     * so a partial run must not report every rule as missing.
     */
    public function testSkipsRunWithoutTheComposerBasedSet(): void
    {
        $this->analyse([__DIR__.'/Fixture/Coverage/PerMinor/drupal-11.3-deprecations.php'], []);
    }

    /**
     * @return \PHPStan\Collectors\Collector[]
     */
    protected function getCollectors(): array
    {
        return [new RegisteredRectorClassCollector()];
    }

    protected function getRule(): Rule
    {
        return new ComposerBasedSetCoverageRule();
    }
}
