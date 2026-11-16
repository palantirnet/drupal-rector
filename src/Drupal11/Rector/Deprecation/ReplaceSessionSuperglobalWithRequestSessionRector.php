<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Services\AddCommentService;
use DrupalRector\Utility\SessionRewriteVisitor;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignOp;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\PostDec;
use PhpParser\Node\Expr\PostInc;
use PhpParser\Node\Expr\PreDec;
use PhpParser\Node\Expr\PreInc;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Unset_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParameterReflection;
use Rector\PhpParser\Node\FileNode;
use Rector\Rector\AbstractRector;
use Rector\Reflection\ReflectionResolver;
use Rector\VersionBonding\Contract\ComposerPackageConstraintInterface;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces custom keys in $_SESSION with \Drupal::request()->getSession().
 *
 * Deprecated in drupal:11.2.0, removed in drupal:12.0.0. Converts every access
 * shape (write, read, `??`, isset, empty, unset, nested access, `++` and `op=`)
 * so that a key never ends up split over two storage locations: Symfony's
 * session stores `set('k')` in the attribute bag, not in the raw
 * `$_SESSION['k']` slot.
 *
 * Rector works one file at a time, so a key is converted completely or not at
 * all. When any access to a key cannot be converted safely (reference alias,
 * by-reference argument, `foreach` by reference, destructuring target, write
 * used as an expression, string interpolation, ...), every access to that key
 * in the file is left alone and a TODO comment is added at the offending
 * statement. A bare `$_SESSION` (no dimension) skips the whole file.
 *
 * The replacement needs no backwards compatibility wrapper:
 * Request::getSession() with get()/set()/remove() exists on every supported
 * core.
 *
 * @see https://www.drupal.org/node/3518914
 * @see https://www.drupal.org/node/3518527
 */
final class ReplaceSessionSuperglobalWithRequestSessionRector extends AbstractRector implements ComposerPackageConstraintInterface, DocumentedRuleInterface
{
    /**
     * Keys owned by Symfony's session storage; these are not deprecated.
     */
    private const INTERNAL_KEYS = ['_sf2_attributes', '_symfony_flashes', '_sf2_meta'];

    private const TODO_MARKER = 'cannot be converted automatically';

    public function __construct(
        private readonly AddCommentService $commentService,
        private readonly ReflectionResolver $reflectionResolver,
    ) {
    }

    public function provideComposerPackageConstraint(): ComposerPackageConstraint
    {
        return new ComposerPackageConstraint('drupal/core', '>=11.2.0 <13.0.0');
    }

    public function getNodeTypes(): array
    {
        return [FileNode::class];
    }

    /**
     * @param FileNode $node
     */
    public function refactor(Node $node): ?Node
    {
        /** @var array<Stmt> $stmts */
        $stmts = $node->stmts;
        $analysis = $this->analyze($stmts);
        if ($analysis === null) {
            return null;
        }

        $changed = $this->addTodoComments($analysis['sites']);

        if ($analysis['actions'] !== []) {
            $traverser = new NodeTraverser(new SessionRewriteVisitor($analysis['actions'], $this->usedVariableNames($node->stmts)));
            /** @var array<Stmt> $rewritten */
            $rewritten = $traverser->traverse($stmts);
            $node->stmts = $rewritten;
            $changed = true;
        }

        return $changed ? $node : null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace custom $_SESSION keys with \\Drupal::request()->getSession()->get()/set()/remove() (drupal:11.2.0)', [
            new CodeSample(
                <<<'CODE_SAMPLE'
$_SESSION['my_key'] = $value;
if (isset($_SESSION['my_key']) && !empty($_SESSION['other'])) {
    $name = $_SESSION['my_key'] ?? 'default';
}
unset($_SESSION['my_key']);
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
\Drupal::request()->getSession()->set('my_key', $value);
if (\Drupal::request()->getSession()->get('my_key') !== NULL && !empty(\Drupal::request()->getSession()->get('other'))) {
    $name = \Drupal::request()->getSession()->get('my_key') ?? 'default';
}
\Drupal::request()->getSession()->remove('my_key');
CODE_SAMPLE,
            ),
        ]);
    }

    /**
     * Classifies every $_SESSION access in the file.
     *
     * @param array<Node> $stmts
     *
     * @return array{actions: array<int, list<array<string, mixed>>>, sites: list<array{stmt: ?Stmt, reason: string}>}|null
     *                                                                                                                      Null when the file does not touch $_SESSION at all
     */
    private function analyze(array $stmts): ?array
    {
        $printer = new Standard();
        /** @var array<string, list<array<string, mixed>>> $byKey */
        $byKey = [];
        /** @var array<string, true> $unsafeKeys */
        $unsafeKeys = [];
        /** @var list<array{stmt: ?Stmt, reason: string}> $sites */
        $sites = [];
        $fileUnsafe = false;
        $dynamicUnsafe = false;
        $found = false;

        $walk = function (Node $node, array $ancestors) use (&$walk, &$byKey, &$unsafeKeys, &$sites, &$fileUnsafe, &$dynamicUnsafe, &$found, $printer): void {
            if ($node instanceof Variable && $node->name === '_SESSION') {
                $found = true;
                $parent = $ancestors[count($ancestors) - 1] ?? null;
                if (!$parent instanceof ArrayDimFetch || $parent->var !== $node) {
                    $fileUnsafe = true;
                    $sites[] = ['stmt' => $this->enclosingStatement($ancestors), 'reason' => '$_SESSION is used without a key'];
                } elseif ($parent->dim === null) {
                    $fileUnsafe = true;
                    $sites[] = ['stmt' => $this->enclosingStatement($ancestors), 'reason' => '$_SESSION[] pushes onto the session root'];
                }
            }

            if ($node instanceof ArrayDimFetch && $node->var instanceof Variable && $node->var->name === '_SESSION' && $node->dim !== null) {
                $key = $this->keyOf($node->dim, $printer);
                if ($key !== null) {
                    $access = $this->classify($node, $ancestors);
                    if (isset($access['unsafe'])) {
                        $unsafeKeys[$key] = true;
                        $sites[] = ['stmt' => $this->enclosingStatement($ancestors), 'reason' => $access['unsafe']];
                        $dynamicUnsafe = $dynamicUnsafe || !$node->dim instanceof String_;
                    } else {
                        $byKey[$key][] = $access;
                    }
                }
            }

            $next = [...$ancestors, $node];
            foreach ($node->getSubNodeNames() as $name) {
                $child = $node->$name;
                if ($child instanceof Node) {
                    $walk($child, $next);
                } elseif (is_array($child)) {
                    foreach ($child as $item) {
                        if ($item instanceof Node) {
                            $walk($item, $next);
                        }
                    }
                }
            }
        };

        foreach ($stmts as $stmt) {
            $walk($stmt, []);
        }

        if (!$found) {
            return null;
        }

        if ($fileUnsafe || $dynamicUnsafe) {
            return ['actions' => [], 'sites' => $sites];
        }

        // An unsafe literal key may alias a dynamic one at runtime, so leave
        // dynamic keys alone together with it.
        $anyUnsafe = $unsafeKeys !== [];
        $actions = [];
        foreach ($byKey as $key => $accesses) {
            if (isset($unsafeKeys[$key]) || ($anyUnsafe && !str_starts_with($key, 's:'))) {
                continue;
            }
            foreach ($accesses as $access) {
                $actions[spl_object_id($access['owner'])][] = $access;
            }
        }

        return ['actions' => $actions, 'sites' => $sites];
    }

    /**
     * @param array<Node> $ancestors
     *
     * @return array<string, mixed>
     *                              Either ['unsafe' => reason], or an access description
     */
    private function classify(ArrayDimFetch $first, array $ancestors): array
    {
        // Climb to the outermost dimension fetch of the chain.
        $outer = $first;
        $index = count($ancestors) - 1;
        while ($index >= 0 && $ancestors[$index] instanceof ArrayDimFetch && $ancestors[$index]->var === $outer) {
            $outer = $ancestors[$index];
            --$index;
        }
        $parent = $ancestors[$index] ?? null;
        $grandParent = $ancestors[$index - 1] ?? null;
        $depth = 1;
        for ($node = $outer; $node !== $first; $node = $node->var) {
            ++$depth;
        }

        $base = ['first' => $first, 'outer' => $outer, 'depth' => $depth, 'owner' => $first, 'kind' => SessionRewriteVisitor::READ];
        $statementLevel = $grandParent instanceof Expression;

        if ($parent instanceof AssignRef) {
            return ['unsafe' => '$_SESSION is aliased by reference'];
        }
        if ($parent instanceof Assign && $parent->var === $outer) {
            return $statementLevel
                ? $this->modification($base, SessionRewriteVisitor::WRITE, $grandParent, $depth > 1)
                : ['unsafe' => 'an assignment to $_SESSION is used as a value'];
        }
        $isModification = ($parent instanceof AssignOp && $parent->var === $outer)
            || $parent instanceof PreInc || $parent instanceof PostInc || $parent instanceof PreDec || $parent instanceof PostDec;
        if ($isModification) {
            return $statementLevel
                ? $this->modification($base, SessionRewriteVisitor::MODIFY, $grandParent, true)
                : ['unsafe' => 'a modification of $_SESSION is used as a value'];
        }
        if ($parent instanceof Unset_) {
            return $this->modification($base, SessionRewriteVisitor::UNSET, $parent, $depth > 1);
        }
        if ($parent instanceof Isset_ && $depth === 1) {
            return ['kind' => SessionRewriteVisitor::ISSET, 'owner' => $parent] + $base;
        }
        if ($parent instanceof Foreach_ && ($parent->valueVar === $outer || $parent->keyVar === $outer)) {
            return ['unsafe' => '$_SESSION is the target of a foreach'];
        }
        if ($parent instanceof Foreach_ && $parent->byRef) {
            return ['unsafe' => '$_SESSION is iterated by reference'];
        }
        if ($parent instanceof ArrayItem) {
            if ($parent->byRef) {
                return ['unsafe' => '$_SESSION is captured by reference'];
            }
            if ($this->isDestructuringTarget($ancestors, $index)) {
                return ['unsafe' => '$_SESSION is a destructuring target'];
            }
        }
        if ($parent instanceof InterpolatedString) {
            return ['unsafe' => '$_SESSION is used inside string interpolation'];
        }
        if ($parent instanceof Arg && $grandParent instanceof CallLike && $this->isPassedByReference($grandParent, $parent)) {
            return ['unsafe' => '$_SESSION is passed to a by-reference parameter'];
        }

        return $base;
    }

    /**
     * @param array<string, mixed> $base
     *
     * @return array<string, mixed>
     */
    private function modification(array $base, string $kind, Node $owner, bool $readModifyWrite): array
    {
        // Depth one writes and unsets map onto a single set()/remove() call;
        // everything else reads the key twice (get and set).
        $readModifyWrite = $readModifyWrite || $kind === SessionRewriteVisitor::MODIFY;
        $dim = $base['first']->dim;
        if ($readModifyWrite && $dim instanceof Expr && $this->hasSideEffects($dim)) {
            return ['unsafe' => 'the session key is an expression with side effects'];
        }

        return ['kind' => $kind, 'owner' => $owner] + $base;
    }

    /**
     * @param array<Node> $ancestors
     */
    private function isDestructuringTarget(array $ancestors, int $index): bool
    {
        // Walk up through nested array literals to the one that may be a target.
        while ($index >= 0 && ($ancestors[$index] instanceof ArrayItem || $ancestors[$index] instanceof Expr\Array_ || $ancestors[$index] instanceof Expr\List_)) {
            --$index;
        }
        $top = $ancestors[$index] ?? null;
        $inner = $ancestors[$index + 1] ?? null;

        return ($top instanceof Assign && $top->var === $inner)
            || ($top instanceof Foreach_ && ($top->valueVar === $inner || $top->keyVar === $inner));
    }

    private function isPassedByReference(CallLike $call, Arg $arg): bool
    {
        if ($arg->byRef) {
            return true;
        }

        $position = array_search($arg, $call->getArgs(), true);
        if (!is_int($position)) {
            return false;
        }

        $reflection = null;
        try {
            $reflection = $this->reflectionResolver->resolveFunctionLikeReflectionFromCall($call);
        } catch (\Throwable) {
            // Fall through to the native lookup below.
        }

        if ($reflection instanceof FunctionReflection || $reflection instanceof MethodReflection) {
            $parameters = [];
            foreach ($reflection->getVariants() as $variant) {
                if (count($variant->getParameters()) > count($parameters)) {
                    $parameters = $variant->getParameters();
                }
            }
            $parameter = $this->parameterAt($parameters, $position, $arg);

            return $parameter !== null && !$parameter->passedByReference()->no();
        }

        // Unresolved (for example when core is not autoloaded): internal PHP
        // functions are still known natively. Unknown userland callees are
        // assumed to take the argument by value.
        if ($call instanceof FuncCall && $call->name instanceof Name && function_exists($call->name->toString())) {
            $parameters = (new \ReflectionFunction($call->name->toString()))->getParameters();
            $parameter = $parameters[$position] ?? ($parameters === [] ? null : $parameters[count($parameters) - 1]);

            return $parameter !== null && $parameter->isPassedByReference() && ($parameter->getPosition() === $position || $parameter->isVariadic());
        }

        return false;
    }

    /**
     * @param array<ParameterReflection> $parameters
     */
    private function parameterAt(array $parameters, int $position, Arg $arg): ?ParameterReflection
    {
        if ($arg->name !== null) {
            foreach ($parameters as $parameter) {
                if ($parameter->getName() === $arg->name->toString()) {
                    return $parameter;
                }
            }

            return null;
        }
        $parameters = array_values($parameters);
        if (isset($parameters[$position])) {
            return $parameters[$position];
        }
        $last = $parameters === [] ? null : $parameters[count($parameters) - 1];

        return $last !== null && $last->isVariadic() ? $last : null;
    }

    private function keyOf(Expr $dim, Standard $printer): ?string
    {
        if ($dim instanceof String_) {
            return in_array($dim->value, self::INTERNAL_KEYS, true) ? null : 's:'.$dim->value;
        }

        return 'e:'.$printer->prettyPrintExpr($dim);
    }

    private function hasSideEffects(Expr $expr): bool
    {
        $visitor = new class extends NodeVisitorAbstract {
            public bool $found = false;

            public function enterNode(Node $node)
            {
                if ($node instanceof CallLike || $node instanceof Assign || $node instanceof AssignOp
                    || $node instanceof AssignRef || $node instanceof PreInc || $node instanceof PostInc
                    || $node instanceof PreDec || $node instanceof PostDec || $node instanceof Expr\Closure
                    || $node instanceof Expr\ArrowFunction || $node instanceof Expr\Yield_ || $node instanceof Expr\YieldFrom
                    || $node instanceof Expr\Include_ || $node instanceof Expr\Exit_ || $node instanceof Expr\Print_
                    || $node instanceof Expr\Throw_
                ) {
                    $this->found = true;
                }

                return null;
            }
        };
        (new NodeTraverser($visitor))->traverse([$expr]);

        return $visitor->found;
    }

    /**
     * @param array<Node> $ancestors
     */
    private function enclosingStatement(array $ancestors): ?Stmt
    {
        for ($i = count($ancestors) - 1; $i >= 0; --$i) {
            if ($ancestors[$i] instanceof Stmt && !$ancestors[$i] instanceof FileNode) {
                return $ancestors[$i];
            }
        }

        return null;
    }

    /**
     * @param array<Node> $stmts
     *
     * @return array<string, true>
     */
    private function usedVariableNames(array $stmts): array
    {
        $visitor = new class extends NodeVisitorAbstract {
            /** @var array<string, true> */
            public array $names = [];

            public function enterNode(Node $node)
            {
                if ($node instanceof Variable && is_string($node->name)) {
                    $this->names[$node->name] = true;
                }

                return null;
            }
        };
        (new NodeTraverser($visitor))->traverse($stmts);

        return $visitor->names;
    }

    /**
     * @param list<array{stmt: ?Stmt, reason: string}> $sites
     *
     * @return bool whether a comment was added
     */
    private function addTodoComments(array $sites): bool
    {
        $reasons = [];
        $statements = [];
        foreach ($sites as $site) {
            if ($site['stmt'] === null) {
                continue;
            }
            $id = spl_object_id($site['stmt']);
            $statements[$id] = $site['stmt'];
            $reasons[$id][$site['reason']] = true;
        }

        $added = false;
        foreach ($statements as $id => $stmt) {
            foreach ($stmt->getComments() as $comment) {
                if (str_contains($comment->getText(), self::TODO_MARKER)) {
                    continue 2;
                }
            }
            $this->commentService->addDrupalRectorComment(
                $stmt,
                sprintf(
                    'Custom keys in $_SESSION are deprecated in drupal:11.2.0, but this access %s (%s). Convert every access to this key to \\Drupal::request()->getSession() get()/set()/remove() by hand, mixing both leaves the value in two places. See https://www.drupal.org/node/3518914',
                    self::TODO_MARKER,
                    implode('; ', array_keys($reasons[$id]))
                )
            );
            $added = true;
        }

        return $added;
    }
}
