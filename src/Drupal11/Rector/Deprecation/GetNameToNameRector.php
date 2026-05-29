<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated TestCase::getName() with name() for PHPUnit 10 compatibility.
 *
 * @see https://www.drupal.org/node/3217904
 */
class GetNameToNameRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated TestCase::getName() with TestCase::name() for PHPUnit 10 compatibility',
            [
                new CodeSample(
                    '$this->getName()',
                    '$this->name()'
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /** @param MethodCall $node */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->name, 'getName')) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('PHPUnit\\Framework\\TestCase'))) {
            return null;
        }

        $args = $node->args;
        if (count($args) === 0) {
            // No args — replace directly.
        } elseif (count($args) === 1) {
            $arg = $args[0];
            if (!$arg instanceof Node\Arg) {
                return null;
            }
            if (!$arg->value instanceof Node\Expr\ConstFetch) {
                return null;
            }
            $constName = $this->getName($arg->value->name);
            if (strtolower((string) $constName) !== 'false') {
                return null;
            }
        } else {
            return null;
        }

        $node->name = new Identifier('name');
        $node->args = [];

        return $node;
    }
}
