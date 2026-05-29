<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
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
final class MigrateSqlGetMigrationPluginManagerRector extends AbstractDrupalCoreRector
{
    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration;

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalIntroducedVersionConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalIntroducedVersionConfiguration::class));
            }
        }
        parent::configure($configuration);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated Sql::getMigrationPluginManager() with $this->migrationPluginManager',
            [
                new ConfiguredCodeSample(
                    '$manager = $this->getMigrationPluginManager();',
                    '$manager = $this->migrationPluginManager;',
                    [new DrupalIntroducedVersionConfiguration('11.0.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
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
