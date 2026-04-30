<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes the deprecated string $root argument from Database::convertDbUrlToConnectionInfo().
 *
 * Deprecated in drupal:11.3.0. The $root parameter is no longer needed.
 * Any third $include_test_drivers argument is shifted to position two.
 *
 * @see https://www.drupal.org/node/3522513
 */
final class RemoveRootFromConvertDbUrlRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove deprecated string $root argument from Database::convertDbUrlToConnectionInfo()',
            [
                new CodeSample(
                    'Database::convertDbUrlToConnectionInfo($url, $this->root, TRUE);',
                    'Database::convertDbUrlToConnectionInfo($url, TRUE);'
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->class, 'Drupal\Core\Database\Database')) {
            return null;
        }
        if (!$this->isName($node->name, 'convertDbUrlToConnectionInfo')) {
            return null;
        }
        if (count($node->args) < 2) {
            return null;
        }

        $secondArg = $node->args[1];
        if (!$secondArg instanceof Arg) {
            return null;
        }
        $secondArgValue = $secondArg->value;

        if ($secondArgValue instanceof ConstFetch) {
            $constName = strtolower((string) $secondArgValue->name);
            if ($constName === 'true' || $constName === 'false' || $constName === 'null') {
                return null;
            }
        } elseif ($secondArgValue instanceof Variable) {
            return null;
        } elseif (
            !$secondArgValue instanceof String_
            && !$secondArgValue instanceof PropertyFetch
            && !$secondArgValue instanceof NullsafePropertyFetch
            && !$secondArgValue instanceof FuncCall
            && !$secondArgValue instanceof StaticPropertyFetch
            && !$secondArgValue instanceof MethodCall
        ) {
            return null;
        }

        array_splice($node->args, 1, 1);

        return $node;
    }
}
