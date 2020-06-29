<?php

namespace DrupalRector\Rector\Deprecation\Base;

use DrupalRector\Utility\AddCommentTrait;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;

/**
 * Replaces deprecated method calls with a new method.
 *
 * What is covered:
 * - Changes the name of the method.
 *
 * Improvement opportunities:
 * - Checks the variable has a certain class.
 *
 */
abstract class MethodToMethodBase extends AbstractRector
{
    use AddCommentTrait;

    /**
     * Deprecated method name.
     *
     * @var string
     */
    protected $deprecatedMethodName;

    /**
     * The replacement method name.
     *
     * @var string
     */
    protected $methodName;

    /**
     * The type of class the method is being called on.
     *
     * @var string
     */
    protected $className;

    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\MethodCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Expr\MethodCall $node */
        // TODO: Check the class to see if it implements $this->className.
        if ($this->getName($node->name) === $this->deprecatedMethodName) {
            $node_var = $node->var->name;

            if ($node->var instanceof Node\Expr\Variable) {
              $node_var = "$$node_var";
            }

            if ($node->var instanceof Node\Expr\MethodCall) {
              $node_var = "$node_var()";
            }

            $this->addDrupalRectorComment($node, "Please confirm that `$node_var` is an instance of `$this->className`. Only the method name and not the class name was checked for this replacement, so this may be a false positive.");
            
            $node->name = new Node\Name($this->methodName);

            return $node;
        }

        return null;
    }
}
