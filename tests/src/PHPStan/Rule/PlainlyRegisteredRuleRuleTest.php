<?php

declare(strict_types=1);

namespace DrupalRector\Tests\PHPStan\Rule;

use DrupalRector\PHPStan\Rule\PlainlyRegisteredRuleRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<PlainlyRegisteredRuleRule>
 */
final class PlainlyRegisteredRuleRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__.'/Fixture/Set/composer-based.php'], [
            [
                '"DrupalRector\Rector\Convert\HookConvertRector" must implement "Rector\VersionBonding\Contract\ComposerPackageConstraintInterface" to be registered here, otherwise it runs on every Drupal version.',
                12,
            ],
        ]);
    }

    public function testSkipsFileThatIsNotTheComposerBasedSet(): void
    {
        $this->analyse([__DIR__.'/Fixture/bound_rule_configuration.php'], []);
    }

    protected function getRule(): Rule
    {
        return new PlainlyRegisteredRuleRule();
    }
}
