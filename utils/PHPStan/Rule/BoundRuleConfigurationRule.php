<?php

declare(strict_types=1);

namespace DrupalRector\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * A rule registered in the composer-based set has to state the exact
 * `drupal/core` version its deprecation was introduced in, so Rector only
 * activates it on a core that has the deprecation.
 *
 * @implements Rule<MethodCall>
 *
 * @see \DrupalRector\Tests\PHPStan\Rule\BoundRuleConfigurationRuleTest
 */
final class BoundRuleConfigurationRule implements Rule
{
    /**
     * @var string
     */
    public const PACKAGE_NAME = 'drupal/core';

    /**
     * @var string
     */
    public const VERSION_CONSTRAINT_REGEX = '#^>=\d+\.\d+\.\d+$#';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Node\Identifier) {
            return [];
        }

        if ($node->name->toString() !== 'ruleWithConfigurationComposerVersionBound') {
            return [];
        }

        $args = $node->getArgs();
        if (count($args) < 4) {
            return [];
        }

        $ruleErrors = [];

        $packageName = $this->resolveConstantString($args[2]->value, $scope);
        if ($packageName !== self::PACKAGE_NAME) {
            $ruleErrors[] = RuleErrorBuilder::message(sprintf(
                'Bind the rule to the "%s" package, "%s" given.',
                self::PACKAGE_NAME,
                $packageName ?? 'a non-literal value'
            ))
                ->identifier('drupalRector.boundRulePackage')
                ->build();
        }

        $versionConstraint = $this->resolveConstantString($args[3]->value, $scope);
        if ($versionConstraint === null || preg_match(self::VERSION_CONSTRAINT_REGEX, $versionConstraint) !== 1) {
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
