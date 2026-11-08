<?php

declare(strict_types=1);

namespace DrupalRector\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * A rule registered in the composer-based set has to state the `drupal/core`
 * version its deprecation was introduced in, so Rector only activates it on a
 * core that has the deprecation.
 *
 * The lower bound is the version the deprecation was introduced in. The upper
 * bound, when given, is the major *after* the one the API was removed in:
 * Rector reads source rather than runtime, so a codebase can still contain a
 * removed call, and those are the calls most worth rewriting. Drupal removes
 * deprecated API only on a major boundary, so the upper bound is always
 * `<N.0.0` and `N` has to be a later major than the lower bound's.
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
    public const VERSION_CONSTRAINT_REGEX = '#^>=(\d+)\.\d+\.\d+(?: <(\d+)\.0\.0)?$#';

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

        $packageName = $this->resolveConstantString($args[2]->value, $scope);
        $versionConstraint = $this->resolveConstantString($args[3]->value, $scope);

        return self::buildErrors($packageName, $versionConstraint);
    }

    /**
     * Holds a constraint declared in the set and one declared on a rule class
     * to the same shape.
     *
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    public static function buildErrors(?string $packageName, ?string $versionConstraint): array
    {
        $ruleErrors = [];

        if ($packageName !== self::PACKAGE_NAME) {
            $ruleErrors[] = RuleErrorBuilder::message(sprintf(
                'Bind the rule to the "%s" package, "%s" given.',
                self::PACKAGE_NAME,
                $packageName ?? 'a non-literal value'
            ))
                ->identifier('drupalRector.boundRulePackage')
                ->build();
        }

        $problem = self::findConstraintProblem($versionConstraint);
        if ($problem !== null) {
            $ruleErrors[] = RuleErrorBuilder::message($problem[0])
                ->identifier($problem[1])
                ->build();
        }

        return $ruleErrors;
    }

    /**
     * Describes what is wrong with a version constraint, or NULL when it is valid.
     *
     * @return array{0: string, 1: string}|null the message and its identifier
     */
    public static function findConstraintProblem(?string $versionConstraint): ?array
    {
        if ($versionConstraint === null || preg_match(self::VERSION_CONSTRAINT_REGEX, $versionConstraint, $matches) !== 1) {
            return [
                sprintf(
                    'Bind the rule to the exact version the deprecation was introduced in, optionally with the major it is removed in, e.g. ">=11.3.0" or ">=11.3.0 <13.0.0", "%s" given.',
                    $versionConstraint ?? 'a non-literal value'
                ),
                'drupalRector.boundRuleVersion',
            ];
        }

        // An absent upper bound is still allowed while the set is being bounded.
        if (!isset($matches[2])) {
            return null;
        }

        if ((int) $matches[2] <= (int) $matches[1]) {
            return [
                sprintf(
                    'Bind the rule to an upper bound that is a later major than its lower bound, "%s" given.',
                    $versionConstraint
                ),
                'drupalRector.boundRuleVersionOrder',
            ];
        }

        return null;
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
