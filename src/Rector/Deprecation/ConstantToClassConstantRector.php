<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\ConstantToClassConfiguration;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated constant with class constant.
 *
 * What is covered:
 * - Replacement with a use statement.
 */
class ConstantToClassConstantRector extends AbstractDrupalCoreRector
{
    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof ConstantToClassConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', ConstantToClassConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated constant use, used in Drupal 8 and later deprecations', [
            new ConfiguredCodeSample(
                '$result = file_unmanaged_copy($source, $destination, DEPRECATED_CONSTANT);',
                '$result = file_unmanaged_copy($source, $destination, \Drupal\MyClass::CONSTANT);',
                [new ConstantToClassConfiguration('DEPRECATED_CONSTANT', 'Drupal\MyClass', 'CONSTANT', '8.0.0')]
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Node\Expr\ConstFetch::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof Node\Expr\ConstFetch);
        assert($configuration instanceof ConstantToClassConfiguration);

        if ($this->getName($node->name) !== $configuration->getDeprecated()) {
            return null;
        }

        return new Node\Expr\ClassConstFetch(
            new Node\Name\FullyQualified($configuration->getClass()),
            new Node\Identifier($configuration->getConstant())
        );
    }
}
