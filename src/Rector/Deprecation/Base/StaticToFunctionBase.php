<?php

namespace DrupalRector\Rector\Deprecation\Base;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;

/**
 * Replaces deprecated static call with a function call.
 *
 * What is covered:
 * - Static replacement
 */
abstract class StaticToFunctionBase extends AbstractRector
{
    /**
     * Deprecated fully qualified class name.
     *
     * @var string
     */
    protected $deprecatedFullyQualifiedClassName;

    /**
     * The deprecated function name.
     *
     * @var string
     */
    protected $deprecatedMethodName;

    /**
     * The replacement function name.
     *
     * @var string
     */
    protected $functionName;

    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\StaticCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Expr\StaticCall $node */
        if ($this->getName($node->name) === $this->deprecatedMethodName && $this->getName($node->class) === $this->deprecatedFullyQualifiedClassName) {

          $method_name = new Node\Name($this->functionName);

          $node = new Node\Expr\FuncCall($method_name, $node->args);

          return $node;
        }

        return null;
    }
}
