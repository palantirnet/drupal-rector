<?php

namespace DrupalRector\Rector\Deprecation\Base;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;

/**
 * Replaces deprecated function call with a function call.
 *
 * What is covered:
 * - Static replacement
 */
abstract class FunctionToFunctionBase extends AbstractRector
{
    /**
     * Deprecated function name.
     *
     * @var string
     */
    protected $deprecatedFunctionName;

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
            Node\Expr\FuncCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
      /** @var Node\Expr\FuncCall $node */
      if ($this->getName($node->name) === $this->deprecatedFunctionName) {

          $method_name = new Node\Name($this->functionName);

          $node = new Node\Expr\FuncCall($method_name, $node->args);

          return $node;
        }

        return null;
    }
}
