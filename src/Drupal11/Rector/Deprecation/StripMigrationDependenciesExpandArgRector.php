<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Strips the removed $expand argument from getMigrationDependencies() calls.
 *
 * Deprecated in drupal:11.0.0, removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3574717
 */
final class StripMigrationDependenciesExpandArgRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Expr\MethodCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\MethodCall);

        if (!$this->isName($node->name, 'getMigrationDependencies')) {
            return null;
        }

        if (count($node->args) === 0) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\migrate\Plugin\MigrationInterface'))) {
            return null;
        }

        $node->args = [];

        return $node;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Strip removed $expand argument from getMigrationDependencies() calls on MigrationInterface (drupal:11.0.0)', [
            new CodeSample(
                '$deps = $migration->getMigrationDependencies(TRUE);',
                '$deps = $migration->getMigrationDependencies();'
            ),
        ]);
    }
}
