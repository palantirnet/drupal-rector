<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated StatementPrefetchIterator::fetchColumn() with fetchField().
 *
 * @see https://www.drupal.org/node/3490200
 */
final class StatementPrefetchIteratorFetchColumnRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Expr\MethodCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Node\Expr\MethodCall) {
            return null;
        }

        if (!$this->isName($node->name, 'fetchColumn')) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Database\StatementPrefetchIterator'))) {
            return null;
        }

        $node->name = new Node\Identifier('fetchField');

        return $node;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replaces deprecated StatementPrefetchIterator::fetchColumn() with fetchField()', [
            new CodeSample(
                '$result = $statement->fetchColumn(0);',
                '$result = $statement->fetchField(0);'
            ),
        ]);
    }
}
