<?php

declare(strict_types=1);

namespace DrupalRector\Tests\PHPStan\Rule;

use DrupalRector\PHPStan\Rule\BoundRuleConfigurationRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<BoundRuleConfigurationRule>
 */
final class BoundRuleConfigurationRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__.'/Fixture/bound_rule_configuration.php'], [
            [
                'Bind the rule to the "drupal/core" package, "drupal/coder" given.',
                11,
            ],
            [
                'Bind the rule to an exact version the deprecation was introduced in, e.g. ">=11.3.0", ">=11.3" given.',
                18,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new BoundRuleConfigurationRule();
    }
}
