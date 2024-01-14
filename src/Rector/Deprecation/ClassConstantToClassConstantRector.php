<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\ValueObject\ClassConstantToClassConstantConfiguration;
use DrupalRector\Rector\ValueObject\ConstantToClassConfiguration;
use PhpParser\Node;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated constant with class constant.
 *
 * What is covered:
 * - Replacement with a use statement.
 */
class ClassConstantToClassConstantRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var ClassConstantToClassConstantConfiguration[]
     */
    private array $constantToClassRenames;

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof ClassConstantToClassConstantConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', ConstantToClassConfiguration::class));
            }
        }

        $this->constantToClassRenames = $configuration;
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
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$value = \Drupal\Core\Routing\RouteObjectInterface::ROUTE_NAME;
$value2 = \Drupal\Core\Routing\RouteObjectInterface::ROUTE_OBJECT;
$value3 = \Drupal\Core\Routing\RouteObjectInterface::CONTROLLER_NAME;
CODE_AFTER
                ,
                [
                    new ClassConstantToClassConstantConfiguration(
                        'Symfony\Cmf\Component\Routing\RouteObjectInterface',
                        'ROUTE_NAME',
                        'Drupal\Core\Routing\RouteObjectInterface',
                        'ROUTE_NAME',
                    ),
                    new ClassConstantToClassConstantConfiguration(
                        'Symfony\Cmf\Component\Routing\RouteObjectInterface',
                        'ROUTE_OBJECT',
                        'Drupal\Core\Routing\RouteObjectInterface',
                        'ROUTE_OBJECT',
                    ),
                    new ClassConstantToClassConstantConfiguration(
                        'Symfony\Cmf\Component\Routing\RouteObjectInterface',
                        'CONTROLLER_NAME',
                        'Drupal\Core\Routing\RouteObjectInterface',
                        'CONTROLLER_NAME',
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

    /**
     * {@inheritdoc}
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\ClassConstFetch);

        foreach ($this->constantToClassRenames as $constantToClassRename) {
            if ($this->getName($node->name) === $constantToClassRename->getDeprecated() && $this->getName($node->class) === $constantToClassRename->getDeprecatedClass()) {
                // We add a fully qualified class name and the parameters in `rector.php` adds the use statement.
                $fully_qualified_class = new Node\Name\FullyQualified($constantToClassRename->getClass());

                $name = new Node\Identifier($constantToClassRename->getConstant());

                return new Node\Expr\ClassConstFetch($fully_qualified_class, $name);
            }
        }

        return null;
    }
}
