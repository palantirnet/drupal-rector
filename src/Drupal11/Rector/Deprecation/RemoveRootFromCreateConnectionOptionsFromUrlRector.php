<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces the deprecated $root argument in Connection::createConnectionOptionsFromUrl() calls with NULL.
 *
 * @see https://www.drupal.org/node/3506931
 * @see https://www.drupal.org/node/3511287
 */
class RemoveRootFromCreateConnectionOptionsFromUrlRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated $root argument in Connection::createConnectionOptionsFromUrl() calls with NULL',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
$connection->createConnectionOptionsFromUrl($url, $root);
CODE_BEFORE,
                    <<<'CODE_AFTER'
$connection->createConnectionOptionsFromUrl($url, NULL);
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class];
    }

    /**
     * @param MethodCall|StaticCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->name, 'createConnectionOptionsFromUrl')) {
            return null;
        }

        if ($node instanceof MethodCall && !$this->isObjectType($node->var, new ObjectType('Drupal\Core\Database\Connection'))) {
            return null;
        }

        if ($node instanceof StaticCall && !$this->isObjectType($node->class, new ObjectType('Drupal\Core\Database\Connection'))) {
            return null;
        }

        // Must have at least two arguments.
        if (count($node->args) < 2) {
            return null;
        }

        $secondArg = $node->args[1];

        // Skip if the argument is already named or unpacked (edge cases).
        if (!$secondArg instanceof Arg) {
            return null;
        }

        // Skip if the second argument is already null.
        $value = $secondArg->value;
        if ($value instanceof ConstFetch && strtolower((string) $value->name) === 'null') {
            return null;
        }

        // Replace the second argument with NULL.
        $node->args[1] = new Arg(new ConstFetch(new Name('NULL')));

        return $node;
    }
}
