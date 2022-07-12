<?php

namespace DrupalRector\Rector\Deprecation\Base;

use DrupalRector\Utility\AddCommentTrait;
use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

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
abstract class MethodToMethodBase extends AbstractRector implements ConfigurableRectorInterface
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

    public function configure(array $configuration): void
    {
        $this->configureNoticesAsComments($configuration);
    }

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
        assert($node instanceof Node\Expr\MethodCall);
        if ($this->getName($node->name) !== $this->deprecatedMethodName) {
            return null;
        }
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
            $this->addDrupalRectorComment(
                $node,
                "Please confirm that `$node_var` is an instance of `$this->className`. Only the method name and not the class name was checked for this replacement, so this may be a false positive."
            );
            $node->name = new Node\Identifier($this->methodName);
            return $node;
        }

        return null;
    }
}
