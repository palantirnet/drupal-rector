<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitor;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes deprecated CachePluginBase::getRowCacheKeys() and getRowId() calls.
 *
 * Both methods are deprecated in drupal:11.4.0 and removed in drupal:13.0.0
 * with no replacement. Handles three patterns:
 * - Inline array item value:  ['keys' => $plugin->getRowCacheKeys($row)]
 * - Variable-first assignment: $keys = $plugin->getRowCacheKeys($row); [...'keys' => $keys]
 * - Delegation method:         public function getRowCacheKeys($row) { return $this->plugin->getRowCacheKeys($row); }
 *
 * @see https://www.drupal.org/node/3564958
 */
final class RemoveViewsRowCacheKeysRector extends AbstractRector
{
    private const DEPRECATED_METHODS = ['getRowCacheKeys', 'getRowId'];

    private const CACHE_PLUGIN_BASE = 'Drupal\views\Plugin\views\cache\CachePluginBase';

    /** @var list<string> variable names whose assignments were removed in the current file */
    private array $removedVarNames = [];

    /**
     * @param array<Node> $nodes
     *
     * @return array<Node>
     */
    public function beforeTraversal(array $nodes): array
    {
        $this->removedVarNames = [];

        return $nodes;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove deprecated CachePluginBase::getRowCacheKeys() and getRowId() calls and array item values',
            [
                new CodeSample(
                    "['keys' => \$cache_plugin->getRowCacheKeys(\$row), 'tags' => []]",
                    "['tags' => []]"
                ),
                new CodeSample(
                    "\$keys = \$cache_plugin->getRowCacheKeys(\$row);\n['keys' => \$keys, 'tags' => []]",
                    "['tags' => []]"
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Array_::class, Expression::class, ClassMethod::class];
    }

    public function refactor(Node $node): int|Node|null
    {
        if ($node instanceof Expression) {
            return $this->refactorExpression($node);
        }

        if ($node instanceof ClassMethod) {
            return $this->refactorClassMethod($node);
        }

        assert($node instanceof Array_);

        return $this->refactorArray($node);
    }

    /** @return NodeVisitor::REMOVE_NODE|null */
    private function refactorExpression(Expression $node): ?int
    {
        if (!$node->expr instanceof Assign) {
            return null;
        }
        $assign = $node->expr;
        if (!$assign->var instanceof Variable) {
            return null;
        }
        if (!$assign->expr instanceof MethodCall) {
            return null;
        }
        if (!$this->isDeprecatedMethodCall($assign->expr)) {
            return null;
        }

        $varName = $this->getName($assign->var);
        if ($varName !== null) {
            $this->removedVarNames[] = $varName;
        }

        return NodeVisitor::REMOVE_NODE;
    }

    /** @return NodeVisitor::REMOVE_NODE|null */
    private function refactorClassMethod(ClassMethod $node): ?int
    {
        if (count($node->stmts ?? []) !== 1) {
            return null;
        }
        $stmt = $node->stmts[0];
        if (!$stmt instanceof Return_) {
            return null;
        }
        if (!$stmt->expr instanceof MethodCall) {
            return null;
        }
        if (!$this->isDeprecatedMethodCall($stmt->expr)) {
            return null;
        }

        return NodeVisitor::REMOVE_NODE;
    }

    private function refactorArray(Array_ $node): ?Array_
    {
        $modified = false;
        $newItems = [];

        foreach ($node->items as $item) {
            if ($item->value instanceof MethodCall
                && $this->isDeprecatedMethodCall($item->value)
            ) {
                $modified = true;
                continue;
            }

            if ($item->value instanceof Variable
                && in_array($this->getName($item->value), $this->removedVarNames, true)
            ) {
                $modified = true;
                continue;
            }

            $newItems[] = $item;
        }

        if (!$modified) {
            return null;
        }
        $node->items = $newItems;

        return $node;
    }

    private function isDeprecatedMethodCall(MethodCall $call): bool
    {
        return $call->name instanceof Identifier
            && in_array($call->name->toString(), self::DEPRECATED_METHODS, true)
            && $this->isObjectType($call->var, new ObjectType(self::CACHE_PLUGIN_BASE));
    }
}
