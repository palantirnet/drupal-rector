<?php

declare(strict_types=1);

namespace DrupalRector\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;

/**
 * Every constraint a rule declares has to name `drupal/core` and the exact
 * version its deprecation was introduced in, so the composer-based set stays
 * comparable across rules.
 *
 * @implements Rule<New_>
 *
 * @see \DrupalRector\Tests\PHPStan\Rule\ComposerPackageConstraintRuleTest
 */
final class ComposerPackageConstraintRule implements Rule
{
    public function getNodeType(): string
    {
        return New_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->class instanceof Node\Name) {
            return [];
        }

        if ($scope->resolveName($node->class) !== ComposerPackageConstraint::class) {
            return [];
        }

        $args = $node->getArgs();
        if (count($args) < 2) {
            return [];
        }

        $ruleErrors = [];

        $packageName = $this->resolveConstantString($args[0]->value, $scope);
        if ($packageName !== BoundRuleConfigurationRule::PACKAGE_NAME) {
            $ruleErrors[] = RuleErrorBuilder::message(sprintf(
                'Bind the rule to the "%s" package, "%s" given.',
                BoundRuleConfigurationRule::PACKAGE_NAME,
                $packageName ?? 'a non-literal value'
            ))
                ->identifier('drupalRector.boundRulePackage')
                ->build();
        }

        $versionConstraint = $this->resolveConstantString($args[1]->value, $scope);
        if ($versionConstraint === null || preg_match(BoundRuleConfigurationRule::VERSION_CONSTRAINT_REGEX, $versionConstraint) !== 1) {
            $ruleErrors[] = RuleErrorBuilder::message(sprintf(
                'Bind the rule to an exact version the deprecation was introduced in, e.g. ">=11.3.0", "%s" given.',
                $versionConstraint ?? 'a non-literal value'
            ))
                ->identifier('drupalRector.boundRuleVersion')
                ->build();
        }

        return $ruleErrors;
    }

    private function resolveConstantString(Node\Expr $expr, Scope $scope): ?string
    {
        $constantStrings = $scope->getType($expr)->getConstantStrings();
        if (count($constantStrings) !== 1) {
            return null;
        }

        return $constantStrings[0]->getValue();
    }
}
