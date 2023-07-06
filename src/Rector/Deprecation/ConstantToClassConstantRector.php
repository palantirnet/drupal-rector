<?php

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
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
     * The deprecated constant.
     *
     * @var string
     */
    protected $deprecatedConstant;

    /**
     * The replacement fully qualified class name.
     *
     * @var string
     */
    protected $constantFullyQualifiedClassName;

    /**
     * The replacement constant.
     *
     * @var string
     */
    protected $constant;

    const DEPRECATED_CONSTANT = 'deprecated_constant';
    const CONSTANT_FULLY_QUALIFIED_CLASS_NAME = 'constant_fully_qualified_class_name';
    const CONSTANT = 'constant';

    /**
     * @param array $configuration
     */
    public function configure(array $configuration): void
    {
        $this->deprecatedConstant = $configuration[static::DEPRECATED_CONSTANT] ?? '';
        $this->constantFullyQualifiedClassName = $configuration[static::CONSTANT_FULLY_QUALIFIED_CLASS_NAME] ?? '';
        $this->constant = $configuration[static::CONSTANT] ?? '';

        if ($this->deprecatedConstant === '' || $this->constantFullyQualifiedClassName === '' || $this->constant === '') {
            throw new \InvalidArgumentException('You must set the deprecated constant, the replacement fully qualified class name and the replacement constant.');
        }
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
                    self::DEPRECATED_CONSTANT => 'DEPRECATED_CONSTANT',
                    self::CONSTANT_FULLY_QUALIFIED_CLASS_NAME => 'Drupal\MyClass',
                    self::CONSTANT => 'CONSTANT',
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
        /** @var Node\Expr\FuncCall $node */
        if ($this->getName($node->name) === $this->deprecatedConstant) {

            // We add a fully qualified class name and the parameters in `rector.php` adds the use statement.
            $fully_qualified_class = new Node\Name\FullyQualified($this->constantFullyQualifiedClassName);

            $name = new Node\Identifier($this->constant);

            $node = new Node\Expr\ClassConstFetch($fully_qualified_class, $name);

            return $node;
        }

        return null;
    }
}
