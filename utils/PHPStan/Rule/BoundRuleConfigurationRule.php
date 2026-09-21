<?php

declare(strict_types=1);

namespace DrupalRector\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * A rule registered in the composer-based set has to say which versions of its
 * package it applies to, so Rector only activates it where the deprecation
 * actually exists.
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
     * The packages a rule may bind itself to.
     *
     * Almost every rule tracks `drupal/core`, but a handful target a
     * deprecation that belongs to one of core's own dependencies, and binding
     * those to `drupal/core` states something untrue. Keep this list short:
     * each entry should be a package whose release cycle a rule genuinely
     * follows.
     *
     * @var list<string>
     */
    public const PACKAGE_NAMES = [
        'drupal/core',
        'phpunit/phpunit',
        'symfony/validator',
    ];

    /**
     * @var string
     */
    public const LOWER_BOUND_REGEX = '#^>=(\d+)\.\d+\.\d+$#';

    /**
     * @var string
     */
    public const UPPER_BOUND_REGEX = '#^<(\d+)\.0\.0$#';

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

        if ($packageName === null || !in_array($packageName, self::PACKAGE_NAMES, true)) {
            $ruleErrors[] = RuleErrorBuilder::message(sprintf(
                'Bind the rule to one of the packages "%s", "%s" given.',
                implode('", "', self::PACKAGE_NAMES),
                $packageName ?? 'a non-literal value'
            ))
                ->identifier('drupalRector.boundRulePackage')
                ->build();
        }

        $problem = self::findConstraintProblem($packageName, $versionConstraint);
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
    public static function findConstraintProblem(?string $packageName, ?string $versionConstraint): ?array
    {
        $malformed = [
            sprintf(
                'Bind the rule to the version the deprecation was introduced in, optionally with the major it is removed in, e.g. ">=11.3.0" or ">=11.3.0 <13.0.0", "%s" given.',
                $versionConstraint ?? 'a non-literal value'
            ),
            'drupalRector.boundRuleVersion',
        ];

        if ($versionConstraint === null) {
            return $malformed;
        }

        $lowerMajor = null;
        $upperMajor = null;
        foreach (explode(' ', $versionConstraint) as $part) {
            if ($lowerMajor === null && preg_match(self::LOWER_BOUND_REGEX, $part, $matches) === 1) {
                $lowerMajor = (int) $matches[1];

                continue;
            }

            if ($upperMajor === null && preg_match(self::UPPER_BOUND_REGEX, $part, $matches) === 1) {
                $upperMajor = (int) $matches[1];

                continue;
            }

            return $malformed;
        }

        if ($lowerMajor === null && $upperMajor === null) {
            return $malformed;
        }

        // An API that was already gone before the rule was written has no
        // meaningful lower bound, but a core deprecation always has one.
        if ($lowerMajor === null && $packageName === 'drupal/core') {
            return [
                sprintf(
                    'Bind the rule to the "drupal/core" version the deprecation was introduced in, "%s" states only an upper bound.',
                    $versionConstraint
                ),
                'drupalRector.boundRuleVersion',
            ];
        }

        if ($lowerMajor !== null && $upperMajor !== null && $upperMajor <= $lowerMajor) {
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
