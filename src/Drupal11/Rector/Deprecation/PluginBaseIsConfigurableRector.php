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
 * Replaces deprecated PluginBase::isConfigurable() with an instanceof check.
 *
 * @see https://www.drupal.org/node/3459533
 * @see https://www.drupal.org/node/2946122
 */
final class PluginBaseIsConfigurableRector extends AbstractDrupalCoreRector
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
        if (!$node instanceof Node\Expr\MethodCall) {
            return null;
        }

        if ($this->getName($node->name) !== 'isConfigurable') {
            return null;
        }

        if ($node->args !== []) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Component\Plugin\PluginBase'))) {
            return null;
        }

        return new Node\Expr\Instanceof_(
            $node->var,
            new Node\Name\FullyQualified('Drupal\Component\Plugin\ConfigurableInterface')
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replaces deprecated PluginBase::isConfigurable() with instanceof ConfigurableInterface', [
            new ConfiguredCodeSample(
                '$this->isConfigurable()',
                '$this instanceof \Drupal\Component\Plugin\ConfigurableInterface',
                [new DrupalIntroducedVersionConfiguration('11.1.0')]
            ),
        ]);
    }
}
