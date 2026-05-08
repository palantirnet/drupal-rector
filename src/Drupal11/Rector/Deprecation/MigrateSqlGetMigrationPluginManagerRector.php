<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated Sql::getMigrationPluginManager() with property access.
 *
 * Deprecated in drupal:9.5.0 and removed in drupal:11.0.0. Subclasses of
 * Sql should access $this->migrationPluginManager directly.
 *
 * @see https://www.drupal.org/node/3439369
 * @see https://www.drupal.org/node/3282894
 */
final class MigrateSqlGetMigrationPluginManagerRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated Sql::getMigrationPluginManager() with $this->migrationPluginManager',
            [
                new CodeSample(
                    '$manager = $this->getMigrationPluginManager();',
                    '$manager = $this->migrationPluginManager;'
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof StaticCall) {
            return $this->refactorStaticCall($node);
        }

        assert($node instanceof MethodCall);
        if (!$node->var instanceof Variable || $node->var->name !== 'this') {
            return null;
        }
        if ($this->getName($node->name) !== 'getMigrationPluginManager') {
            return null;
        }
        if ($node->args !== []) {
            return null;
        }
        if (!$this->isObjectType($node->var, new ObjectType('Drupal\migrate\Plugin\migrate\id_map\Sql'))) {
            return null;
        }

        return new PropertyFetch(new Variable('this'), 'migrationPluginManager');
    }

    private function refactorStaticCall(StaticCall $node): ?Node
    {
        if ($this->getName($node->name) !== 'getMigrationPluginManager') {
            return null;
        }
        if ($node->args !== []) {
            return null;
        }
        if (!$node->class instanceof Name || $node->class->toString() !== 'parent') {
            return null;
        }

        return new PropertyFetch(new Variable('this'), 'migrationPluginManager');
    }
}
