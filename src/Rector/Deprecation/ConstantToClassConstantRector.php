<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

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
class ConstantToClassConstantRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var ConstantToClassConfiguration[]
     */
    private array $constantToClassRenames;

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof ConstantToClassConfiguration)) {
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
        return new RuleDefinition('Fixes deprecated contant use, used in Drupal 8 and 9 deprecations', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$result = file_unmanaged_copy($source, $destination, DEPRECATED_CONSTANT);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$result = file_unmanaged_copy($source, $destination, \Drupal\MyClass::CONSTANT);
CODE_AFTER
                ,
                [
                    new ConstantToClassConfiguration(
                        'DEPRECATED_CONSTANT',
                        'Drupal\MyClass',
                        'CONSTANT'
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
            Node\Expr\ConstFetch::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\ConstFetch);

        foreach ($this->constantToClassRenames as $constantToClassRename) {
            if ($this->getName($node->name) === $constantToClassRename->getDeprecated()) {
                // We add a fully qualified class name and the parameters in `rector.php` adds the use statement.
                $fully_qualified_class = new Node\Name\FullyQualified($constantToClassRename->getClass());

                $name = new Node\Identifier($constantToClassRename->getConstant());

                return new Node\Expr\ClassConstFetch($fully_qualified_class, $name);
            }
        }

        return null;
    }
}
