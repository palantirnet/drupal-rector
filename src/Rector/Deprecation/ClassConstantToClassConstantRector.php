<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\ClassConstantToClassConstantConfiguration;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated constant with class constant.
 *
 * What is covered:
 * - Replacement with a use statement.
 */
class ClassConstantToClassConstantRector extends AbstractDrupalCoreRector
{
    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof ClassConstantToClassConstantConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', ClassConstantToClassConstantConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated class contant use, used in Drupal 9.1 deprecations', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$value = Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_NAME;
$value2 = Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_OBJECT;
$value3 = Symfony\Cmf\Component\Routing\RouteObjectInterface::CONTROLLER_NAME;
CODE_BEFORE,
                <<<'CODE_AFTER'
$value = \Drupal\Core\Routing\RouteObjectInterface::ROUTE_NAME;
$value2 = \Drupal\Core\Routing\RouteObjectInterface::ROUTE_OBJECT;
$value3 = \Drupal\Core\Routing\RouteObjectInterface::CONTROLLER_NAME;
CODE_AFTER,
                [
                    new ClassConstantToClassConstantConfiguration(
                        'Symfony\Cmf\Component\Routing\RouteObjectInterface',
                        'ROUTE_NAME',
                        'Drupal\Core\Routing\RouteObjectInterface',
                        'ROUTE_NAME',
                        '9.1.0',
                    ),
                    new ClassConstantToClassConstantConfiguration(
                        'Symfony\Cmf\Component\Routing\RouteObjectInterface',
                        'ROUTE_OBJECT',
                        'Drupal\Core\Routing\RouteObjectInterface',
                        'ROUTE_OBJECT',
                        '9.1.0',
                    ),
                    new ClassConstantToClassConstantConfiguration(
                        'Symfony\Cmf\Component\Routing\RouteObjectInterface',
                        'CONTROLLER_NAME',
                        'Drupal\Core\Routing\RouteObjectInterface',
                        'CONTROLLER_NAME',
                        '9.1.0',
                    ),
                ]
            ),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\ClassConstFetch::class,
        ];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof Node\Expr\ClassConstFetch);
        assert($configuration instanceof ClassConstantToClassConstantConfiguration);

        if ($this->getName($node->name) !== $configuration->getDeprecated() || $this->getName($node->class) !== $configuration->getDeprecatedClass()) {
            return null;
        }

        // We add a fully qualified class name and the parameters in `rector.php` adds the use statement.
        return new Node\Expr\ClassConstFetch(
            new Node\Name\FullyQualified($configuration->getClass()),
            new Node\Identifier($configuration->getConstant())
        );
    }
}
