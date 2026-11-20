<?php

declare(strict_types=1);

namespace DrupalRector\Utility;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Unset_;
use PhpParser\NodeVisitorAbstract;

/**
 * Applies the $_SESSION to \Drupal::request()->getSession() rewrites that
 * ReplaceSessionSuperglobalWithRequestSessionRector planned.
 *
 * Children are rewritten before their parents (leaveNode), so the planned
 * statement-level rewrites see already converted keys and values.
 *
 * Each action is keyed by the spl_object_id() of its owner node: the first
 * dimension fetch for plain reads, the statement for writes, modifications and
 * unsets, the isset() expression for isset() with a top level key.
 */
final class SessionRewriteVisitor extends NodeVisitorAbstract
{
    public const READ = 'read';

    public const WRITE = 'write';

    public const MODIFY = 'modify';

    public const UNSET = 'unset';

    public const ISSET = 'isset';

    /**
     * @param array<int, list<array<string, mixed>>> $actions
     * @param array<string, true>                    $usedVariableNames variables that already exist in the file
     */
    public function __construct(
        private readonly array $actions,
        private readonly array $usedVariableNames,
    ) {
    }

    /**
     * @return Node|list<Node>|null
     */
    public function leaveNode(Node $node)
    {
        $actions = $this->actions[spl_object_id($node)] ?? null;
        if ($actions === null) {
            return null;
        }

        if ($node instanceof ArrayDimFetch) {
            return $this->sessionCall('get', $this->dimOf($node));
        }
        if ($node instanceof Isset_) {
            return $this->rewriteIsset($node, $actions);
        }
        if ($node instanceof Expression) {
            return $this->rewriteStatement($node, $actions[0]);
        }
        if ($node instanceof Unset_) {
            return $this->rewriteUnset($node, $actions);
        }

        return null;
    }

    /**
     * @param list<array<string, mixed>> $actions
     */
    private function rewriteIsset(Isset_ $node, array $actions): Expr
    {
        $converted = [];
        foreach ($node->vars as $var) {
            $expr = null;
            foreach ($actions as $action) {
                if ($action['outer'] === $var) {
                    $expr = new NotIdentical($this->sessionCall('get', $this->dimOf($action['first'])), new ConstFetch(new Name('NULL')));
                }
            }
            $converted[] = $expr ?? new Isset_([$var]);
        }

        $result = array_shift($converted);
        foreach ($converted as $next) {
            $result = new BooleanAnd($result, $next);
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $action
     *
     * @return list<Stmt>
     */
    private function rewriteStatement(Expression $statement, array $action): array
    {
        $expr = $statement->expr;
        /** @var ArrayDimFetch $first */
        $first = $action['first'];
        /** @var Expr $key */
        $key = $first->dim;

        if ($action['kind'] === self::WRITE && $action['depth'] === 1 && $expr instanceof Assign) {
            return $this->withComments([new Expression($this->sessionCall('set', $key, $expr->expr))], $statement);
        }

        // Read-modify-write on a local copy of the session value. Writing
        // below a missing key creates the array, as $_SESSION did.
        $name = $this->variableName($key);
        $initial = $this->sessionCall('get', $key);
        if ($action['depth'] > 1) {
            $initial = new Coalesce($initial, new Expr\Array_());
        }
        $this->replaceFirst($expr, $action, new Variable($name));

        return $this->withComments([
            new Expression(new Assign(new Variable($name), $initial)),
            $statement,
            new Expression($this->sessionCall('set', $this->deepClone($first->dim), new Variable($name))),
        ], $statement);
    }

    /**
     * @param list<array<string, mixed>> $actions
     *
     * @return list<Stmt>
     */
    private function rewriteUnset(Unset_ $statement, array $actions): array
    {
        $byOuter = [];
        foreach ($actions as $action) {
            $byOuter[spl_object_id($action['outer'])] = $action;
        }

        $result = [];
        $plainVars = [];
        foreach ($statement->vars as $var) {
            $action = $byOuter[spl_object_id($var)] ?? null;
            if ($action === null) {
                $plainVars[] = $var;
                continue;
            }
            if ($plainVars !== []) {
                $result[] = new Unset_($plainVars);
                $plainVars = [];
            }
            /** @var ArrayDimFetch $first */
            $first = $action['first'];
            /** @var Expr $key */
            $key = $first->dim;
            if ($action['depth'] === 1) {
                $result[] = new Expression($this->sessionCall('remove', $key));
                continue;
            }
            // Unsetting below a key: edit a copy and store it again.
            $name = $this->variableName($key);
            $this->replaceFirst($var, $action, new Variable($name));
            $result[] = new Expression(new Assign(new Variable($name), $this->sessionCall('get', $key)));
            $result[] = new Unset_([$var]);
            $result[] = new Expression($this->sessionCall('set', $this->deepClone($first->dim), new Variable($name)));
        }
        if ($plainVars !== []) {
            $result[] = new Unset_($plainVars);
        }

        return $this->withComments($result, $statement);
    }

    /**
     * Swaps the first dimension fetch (`$_SESSION['k']`) inside the access for
     * a replacement, leaving the remaining dimensions in place.
     *
     * @param array<string, mixed> $action
     */
    private function replaceFirst(Node $holder, array $action, Expr $replacement): void
    {
        $first = $action['first'];
        if ($action['outer'] === $first) {
            // The modified expression: assign, op-assign, ++ or --.
            assert(property_exists($holder, 'var'));
            $holder->var = $replacement;

            return;
        }

        /** @var ArrayDimFetch $chain */
        $chain = $action['outer'];
        while ($chain->var !== $first) {
            $chain = $chain->var;
        }
        $chain->var = $replacement;
    }

    /**
     * @param list<Stmt> $statements
     *
     * @return list<Stmt>
     */
    private function withComments(array $statements, Stmt $original): array
    {
        $comments = $original->getComments();
        $original->setAttribute('comments', []);
        $statements[0]->setAttribute('comments', $comments);

        return $statements;
    }

    private function dimOf(ArrayDimFetch $fetch): Expr
    {
        // Pushes ($_SESSION[]) are never planned, so a dimension always exists.
        assert($fetch->dim instanceof Expr);

        return $fetch->dim;
    }

    private function sessionCall(string $method, Expr $key, ?Expr $value = null): MethodCall
    {
        $args = [new Arg($key)];
        if ($value !== null) {
            $args[] = new Arg($value);
        }

        return new MethodCall(
            new MethodCall(new StaticCall(new FullyQualified('Drupal'), 'request'), 'getSession'),
            $method,
            $args
        );
    }

    private function variableName(Expr $key): string
    {
        $base = 'session_value';
        if ($key instanceof Node\Scalar\String_) {
            $sanitized = trim((string) preg_replace('/\W+/', '_', $key->value), '_');
            if ($sanitized !== '') {
                $base = 'session_'.$sanitized;
            }
        }

        $name = $base;
        for ($suffix = 2; isset($this->usedVariableNames[$name]); ++$suffix) {
            $name = $base.'_'.$suffix;
        }

        return $name;
    }

    /**
     * Copies an expression so it can appear in two places.
     */
    private function deepClone(Expr $node): Expr
    {
        /** @var Expr $clone */
        $clone = $this->cloneTree($node);

        return $clone;
    }

    private function cloneTree(Node $node): Node
    {
        $clone = clone $node;
        foreach ($clone->getSubNodeNames() as $name) {
            $child = $clone->$name;
            if ($child instanceof Node) {
                $clone->$name = $this->cloneTree($child);
            } elseif (is_array($child)) {
                foreach ($child as $key => $item) {
                    if ($item instanceof Node) {
                        $child[$key] = $this->cloneTree($item);
                    }
                }
                $clone->$name = $child;
            }
        }

        return $clone;
    }
}
