<?php

declare(strict_types=1);

namespace DrupalRector\Tests\PHPStan\Rule;

use DrupalRector\PHPStan\Rule\ComposerPackageConstraintRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<ComposerPackageConstraintRule>
 */
final class ComposerPackageConstraintRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__.'/Fixture/composer_package_constraint.php'], [
            [
                'Bind the rule to the "drupal/core" package, "drupal/coder" given.',
                14,
            ],
            [
                'Bind the rule to an exact version the deprecation was introduced in, e.g. ">=11.3.0", "^11.3" given.',
                19,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new ComposerPackageConstraintRule();
    }
}
