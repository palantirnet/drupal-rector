<?php

namespace DrupalRector\Rector\Deprecation\Base;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;

/**
 * Replaces deprecated constant with class constant.
 *
 * What is covered:
 * - Replacement with a use statement.
 */
abstract class ConstantToClassConstantBase extends AbstractRector
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
