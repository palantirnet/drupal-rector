<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Strips the removed $expand argument from getMigrationDependencies() calls.
 *
 * Deprecated in drupal:11.0.0, removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3574717
 * @see https://www.drupal.org/node/3442785
 */
final class StripMigrationDependenciesExpandArgRector extends AbstractDrupalCoreRector
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

    public function getNodeTypes(): array
    {
        return [Node\Expr\MethodCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
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

        $cloned = clone $node;
        $cloned->args = [];

        return $cloned;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Strip removed $expand argument from getMigrationDependencies() calls on MigrationInterface (drupal:11.0.0)', [
            new ConfiguredCodeSample(
                '$deps = $migration->getMigrationDependencies(TRUE);',
                '$deps = $migration->getMigrationDependencies();',
                [new DrupalIntroducedVersionConfiguration('11.0.0')]
            ),
        ]);
    }
}
