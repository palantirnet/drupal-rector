<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\ValueObject\ConstantToClass;
use PhpParser\Node;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\Renaming\Contract\MethodCallRenameInterface;
use RectorPrefix202304\Webmozart\Assert\Assert;
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
     * @var ConstantToClass[]
     */
    private array $constantToClassRenames;

    /**
     * @param array $configuration
     */
    public function configure(array $configuration): void
    {
        Assert::allIsAOf($configuration, ConstantToClass::class);
        $this->constantToClassRenames = $configuration;
    }

    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated contant use', [
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
                    new ConstantToClass(
                        'DEPRECATED_CONSTANT',
                        'Drupal\MyClass',
                        'CONSTANT'
                    ),
                ]
            )
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\ConstFetch::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Expr\ConstFetch $node */
        foreach ( $this->constantToClassRenames as $constantToClassRename) {
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
