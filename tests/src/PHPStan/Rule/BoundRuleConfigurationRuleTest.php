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
                'Bind the rule to the exact version the deprecation was introduced in, optionally with the major it is removed in, e.g. ">=11.3.0" or ">=11.3.0 <13.0.0", ">=11.3" given.',
                18,
            ],
            [
                'Bind the rule to the exact version the deprecation was introduced in, optionally with the major it is removed in, e.g. ">=11.3.0" or ">=11.3.0 <13.0.0", ">=11.3.0 <12.1.0" given.',
                24,
            ],
            [
                'Bind the rule to an upper bound that is a later major than its lower bound, ">=11.3.0 <11.0.0" given.',
                27,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new BoundRuleConfigurationRule();
    }
}
