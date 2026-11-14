<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Convert;

use PhpParser\Node;
use Rector\Config\RectorConfig;
use Rector\PhpParser\NodeTraverser\RectorNodeTraverser;
use Rector\ValueObject\Application\File;

/**
 * Runs the other active Rector rules over a node list from inside a rule.
 *
 * Why this exists
 * ---------------
 * Rector's traverser is strictly pre-order: every rule that matches a node runs
 * on it before any of its children are visited, and rules have no "leave node"
 * (after-children) hook. A rule that moves or copies a subtree somewhere else
 * therefore sees that subtree before the other rules had a chance to fix it.
 * HookConvertRector is such a rule. On a `Function_` it copies the body into a
 * brand-new hook class, then removes the function (children are never visited)
 * or turns it into a `#[LegacyHook]` wrapper. The hook class is a separate file
 * written from `__destruct()`, so the deprecation rules never see it. Rector's
 * re-run-until-unchanged loop does not help: by then the function body is gone.
 *
 * Workaround
 * ----------
 * Right before the body is copied, HookConvertRector asks this class to run the
 * regular rule pipeline over the body, using Rector's own traverser, in a loop
 * until no rule reports a change (a single traversal is not enough: rules can
 * chain, and Rector does not descend into a node replaced by another class).
 * The owning rule is inert while this runs (see HookConvertRector::$nested).
 *
 * Wiring
 * ------
 * The traverser depends on the rule registry, which depends on all rules
 * including HookConvertRector, so it cannot be constructor-injected. It is
 * fetched lazily from the container, which the rule receives through
 * RectorConfig::afterResolving() (see register()). Without that wiring, or when
 * any Rector internal used here is missing, run() returns the nodes unchanged
 * and the rule behaves as if this class did not exist.
 *
 * Rector internals relied on: RectorNodeTraverser::traverse(),
 * File::getRectorWithLineChanges(), Container::make() and
 * Container::afterResolving() (callback gets the instance and the container).
 *
 * @internal
 */
final class NestedRuleRunner
{
    private const MAX_PASSES = 10;

    private ?object $traverser = null;

    private function __construct(private readonly object $container)
    {
    }

    /**
     * Gives HookConvertRector (or a subclass) access to the container.
     *
     * Call it from a Rector config next to `$rectorConfig->rule()`. Safe to
     * call on a Rector version without the required internals: it is a no-op.
     */
    public static function register(RectorConfig $rectorConfig): void
    {
        if (!self::has($rectorConfig, 'afterResolving')) {
            return;
        }
        $rectorConfig->afterResolving(
            HookConvertRector::class,
            static function (object $rule, object $container): void {
                $rule->useNestedRuleRunner(self::create($container));
            }
        );
    }

    public static function create(object $container): ?self
    {
        if (!class_exists(RectorNodeTraverser::class)
            || !self::has(File::class, 'getRectorWithLineChanges')
            || !self::has($container, 'make')) {
            return null;
        }

        return new self($container);
    }

    /**
     * A guard for other Rector versions, which static analysis cannot narrow
     * for the installed one.
     */
    private static function has(object|string $target, string $method): bool
    {
        return method_exists($target, $method);
    }

    /**
     * @template T of Node
     *
     * @param T[] $stmts
     *
     * @return T[]
     */
    public function run(array $stmts, File $file): array
    {
        $this->traverser ??= $this->container->make(RectorNodeTraverser::class);
        for ($pass = 0; $pass < self::MAX_PASSES; ++$pass) {
            $before = count($file->getRectorWithLineChanges());
            $stmts = $this->traverser->traverse($stmts, $file);
            if (count($file->getRectorWithLineChanges()) === $before) {
                break;
            }
        }

        return $stmts;
    }
}
