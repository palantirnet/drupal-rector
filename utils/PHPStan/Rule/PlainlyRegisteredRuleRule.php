<?php

declare(strict_types=1);

namespace DrupalRector\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Rector\VersionBonding\Contract\ComposerPackageConstraintInterface;

/**
 * A rule that takes no configuration cannot state its version in the
 * composer-based set, so it has to declare it on the rule class instead —
 * without it the rule would run on every Drupal version.
 *
 * @implements Rule<MethodCall>
 *
 * @see \DrupalRector\Tests\PHPStan\Rule\PlainlyRegisteredRuleRuleTest
 */
final class PlainlyRegisteredRuleRule implements Rule
{
    /**
     * @var string
     */
    public const COMPOSER_BASED_SET_FILE_NAME = 'composer-based.php';

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (basename($scope->getFile()) !== self::COMPOSER_BASED_SET_FILE_NAME) {
            return [];
        }

        if (!$node->name instanceof Node\Identifier) {
            return [];
        }

        if ($node->name->toString() !== 'rule') {
            return [];
        }

        $args = $node->getArgs();
        if ($args === []) {
            return [];
        }

        $constantStrings = $scope->getType($args[0]->value)->getConstantStrings();
        if (count($constantStrings) !== 1) {
            return [];
        }

        $rectorClass = $constantStrings[0]->getValue();
        if (is_a($rectorClass, ComposerPackageConstraintInterface::class, true)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(sprintf(
                '"%s" must implement "%s" to be registered here, otherwise it runs on every Drupal version.',
                $rectorClass,
                ComposerPackageConstraintInterface::class
            ))
                ->identifier('drupalRector.unboundRule')
                ->build(),
        ];
    }
}
