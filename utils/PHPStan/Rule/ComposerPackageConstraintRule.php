<?php

declare(strict_types=1);

namespace DrupalRector\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;

/**
 * Every constraint a rule declares has to name an allowed package and the
 * version its deprecation was introduced in, so the composer-based set stays
 * comparable across rules. It may also state the major the API is removed in.
 *
 * The checks live in BoundRuleConfigurationRule::buildErrors(), so a constraint
 * declared here and one declared in the set are held to the same shape.
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

        return BoundRuleConfigurationRule::buildErrors(
            $this->resolveConstantString($args[0]->value, $scope),
            $this->resolveConstantString($args[1]->value, $scope)
        );
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
