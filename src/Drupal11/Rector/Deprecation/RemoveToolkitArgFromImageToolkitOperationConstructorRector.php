<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitor;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes the deprecated $toolkit argument from ImageToolkitOperationBase subclasses.
 *
 * ImageToolkitOperationBase::__construct() deprecated its ImageToolkitInterface
 * $toolkit 4th argument in drupal:11.4.0 (removed in drupal:13.0.0). The plugin
 * manager now injects the toolkit via setToolkit() after instantiation, enabling
 * constructor autowiring. Only transforms when $toolkit appears exactly once in
 * the constructor body (as the parent::__construct() argument).
 *
 * @see https://www.drupal.org/node/3559481
 * @see https://www.drupal.org/node/3562304
 */
final class RemoveToolkitArgFromImageToolkitOperationConstructorRector extends AbstractRector
{
    private const TOOLKIT_INTERFACE = 'Drupal\\Core\\ImageToolkit\\ImageToolkitInterface';

    private const OPERATION_BASE = 'Drupal\\Core\\ImageToolkit\\ImageToolkitOperationBase';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove deprecated $toolkit argument from ImageToolkitOperationBase::__construct(). The toolkit is now injected via setToolkit() by the plugin manager.',
            [new CodeSample(
                <<<'CODE'
use Drupal\Core\ImageToolkit\ImageToolkitInterface;
use Drupal\Core\ImageToolkit\ImageToolkitOperationBase;
use Psr\Log\LoggerInterface;

class MyOperation extends ImageToolkitOperationBase {
    public function __construct(array $configuration, $plugin_id, array $plugin_definition, ImageToolkitInterface $toolkit, LoggerInterface $logger) {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $toolkit, $logger);
    }
}
CODE,
                <<<'CODE'
use Drupal\Core\ImageToolkit\ImageToolkitOperationBase;
use Psr\Log\LoggerInterface;

class MyOperation extends ImageToolkitOperationBase {
    public function __construct(array $configuration, $plugin_id, array $plugin_definition, LoggerInterface $logger) {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $logger);
    }
}
CODE
            )]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /** @param Class_ $node */
    public function refactor(Node $node): ?Node
    {
        // Only target direct subclasses of ImageToolkitOperationBase. The
        // 4-arg `$toolkit` shape is specific to that base class; matching by
        // shape alone risks rewriting unrelated classes that coincidentally
        // share the constructor signature.
        if ($node->extends === null
            || $this->getName($node->extends) !== self::OPERATION_BASE
        ) {
            return null;
        }

        $constructor = $node->getMethod('__construct');
        if ($constructor === null) {
            return null;
        }

        $params = $constructor->params;

        // We need at least 5 params (configuration, plugin_id, plugin_definition, toolkit, logger).
        if (count($params) < 5) {
            return null;
        }

        // Verify the 4th param (index 3) is typed as ImageToolkitInterface.
        $toolkitParam = $params[3];
        if ($toolkitParam->type === null) {
            return null;
        }

        $typeName = $this->getName($toolkitParam->type);
        if ($typeName !== self::TOOLKIT_INTERFACE) {
            return null;
        }

        $toolkitVarName = $this->getName($toolkitParam->var);
        if ($toolkitVarName === null) {
            return null;
        }

        // Count all usages of the $toolkit variable inside the constructor body
        // to ensure it is only passed to parent::__construct(). Skip the
        // bodies of nested closures and arrow-functions — a `$toolkit`
        // parameter or `use ($toolkit)` capture there shadows the outer
        // variable and must not affect the outer-scope count.
        $toolkitUsageCount = 0;
        $this->traverseNodesWithCallable($constructor->stmts ?? [], function (Node $innerNode) use ($toolkitVarName, &$toolkitUsageCount): null|int {
            if ($innerNode instanceof Closure || $innerNode instanceof ArrowFunction) {
                return NodeVisitor::DONT_TRAVERSE_CHILDREN;
            }
            if ($innerNode instanceof Variable && $this->isName($innerNode, $toolkitVarName)) {
                ++$toolkitUsageCount;
            }

            return null;
        });

        // If $toolkit is used more than once or not at all in body, skip.
        if ($toolkitUsageCount !== 1) {
            return null;
        }

        // Locate parent::__construct() and confirm $toolkit is its 4th arg,
        // then remove that arg.
        $parentCallUpdated = false;
        $this->traverseNodesWithCallable($constructor->stmts ?? [], function (Node $innerNode) use ($toolkitVarName, &$parentCallUpdated): ?Node {
            if (!$innerNode instanceof StaticCall) {
                return null;
            }
            if (!$this->isName($innerNode->class, 'parent') || !$this->isName($innerNode->name, '__construct')) {
                return null;
            }
            if (!isset($innerNode->args[3]) || !$innerNode->args[3] instanceof Arg) {
                return null;
            }
            $arg3Value = $innerNode->args[3]->value;
            if (!$arg3Value instanceof Variable || !$this->isName($arg3Value, $toolkitVarName)) {
                return null;
            }
            array_splice($innerNode->args, 3, 1);
            $parentCallUpdated = true;

            return $innerNode;
        });

        if (!$parentCallUpdated) {
            return null;
        }

        // Remove the $toolkit parameter from the constructor signature.
        array_splice($constructor->params, 3, 1);

        return $node;
    }
}
