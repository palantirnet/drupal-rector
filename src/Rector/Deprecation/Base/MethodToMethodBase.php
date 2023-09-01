<?php

namespace DrupalRector\Rector\Deprecation\Base;

use DrupalRector\Utility\AddCommentService;
use PhpParser\Node;
use PHPStan\Type\ObjectType;
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
     * @var \DrupalRector\Utility\AddCommentService
     */
    private AddCommentService $commentService;

    public function __construct(AddCommentService $commentService) {
        $this->commentService = $commentService;
    }


    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\Expression::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Stmt\Expression);

        $isMethodCall = $node->expr instanceof Node\Expr\MethodCall;
        $isAssignedMethodCall = $node->expr instanceof Node\Expr\Assign && $node->expr->expr instanceof Node\Expr\MethodCall;

        if(!$isMethodCall && !$isAssignedMethodCall){
            return null;
        }

        if ($isMethodCall && $this->getName($node->expr->name) !== $this->deprecatedMethodName) {
            return null;
        }

        if ($isAssignedMethodCall && $this->getName($node->expr->expr->name) !== $this->deprecatedMethodName) {
            return null;
        }

        if ($isMethodCall) {
            $methodNode = $this->refactorNode($node->expr, $node);
            if (is_null($methodNode)){
                return null;
            }
            $node->expr = $methodNode;

        } elseif ($isAssignedMethodCall) {
            $methodNode = $this->refactorNode($node->expr->expr, $node);
            if (is_null($methodNode)){
                return null;
            }
            $node->expr->expr = $methodNode;
        }

        return $node;
    }

    public function refactorNode(Node\Expr\MethodCall $node, Node\Stmt\Expression $statement): ?Node\Expr\MethodCall
    {
        $callerType = $this->nodeTypeResolver->getType($node->var);
        $expectedType = new ObjectType($this->className);

        $isSuperOf = $expectedType->isSuperTypeOf($callerType);
        if ($isSuperOf->yes()) {
            $node->name = new Node\Identifier($this->methodName);
            return $node;
        }

        if ($isSuperOf->maybe()) {
            $node_var = $node->var->name;

            if ($node->var instanceof Node\Expr\Variable) {
                $node_var = "$$node_var";
            }
            if ($node->var instanceof Node\Expr\MethodCall) {
                $node_var = "$node_var()";
            }
            $this->commentService->addDrupalRectorComment(
                $statement,
                "Please confirm that `$node_var` is an instance of `$this->className`. Only the method name and not the class name was checked for this replacement, so this may be a false positive."
            );
            $node->name = new Node\Identifier($this->methodName);

            return $node;
        }
        return null;
    }
}
