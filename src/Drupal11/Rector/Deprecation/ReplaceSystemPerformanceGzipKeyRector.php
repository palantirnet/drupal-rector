<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated system.performance css.gzip/js.gzip config keys.
 *
 * The config keys css.gzip and js.gzip were deprecated in drupal:11.4.0
 * and removed in drupal:12.0.0. Use css.compress and js.compress instead.
 *
 * @see https://www.drupal.org/node/3184242
 */
final class ReplaceSystemPerformanceGzipKeyRector extends AbstractRector
{
    private const CONFIG_ACCESSOR_METHODS = ['config', 'get', 'getEditable'];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated system.performance css.gzip/js.gzip config keys with css.compress/js.compress',
            [
                new CodeSample(
                    "\\Drupal::config('system.performance')->get('css.gzip');",
                    "\\Drupal::config('system.performance')->get('css.compress');"
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof MethodCall);
        if (!$this->isNames($node->name, ['get', 'set'])) {
            return null;
        }
        if (empty($node->args)) {
            return null;
        }
        $firstArg = $node->args[0];
        if (!$firstArg instanceof Arg) {
            return null;
        }
        $keyExpr = $firstArg->value;
        if (!$keyExpr instanceof String_) {
            return null;
        }
        $key = $keyExpr->value;
        if ($key !== 'css.gzip' && $key !== 'js.gzip') {
            return null;
        }
        if (!$this->isSystemPerformanceConfigReceiver($node->var)) {
            return null;
        }
        $newKey = ($key === 'css.gzip') ? 'css.compress' : 'js.compress';
        $node->args[0] = new Arg(new String_($newKey));

        return $node;
    }

    private function isSystemPerformanceConfigReceiver(Node $receiver): bool
    {
        $current = $receiver;
        while ($current instanceof MethodCall) {
            if ($this->isNames($current->name, self::CONFIG_ACCESSOR_METHODS)) {
                if (!empty($current->args) && $current->args[0] instanceof Arg) {
                    $arg = $current->args[0]->value;
                    if ($arg instanceof String_ && $arg->value === 'system.performance') {
                        return true;
                    }
                }
            }
            $current = $current->var;
        }
        if ($current instanceof StaticCall) {
            if ($this->isName($current->name, 'config') && !empty($current->args)) {
                $arg = $current->args[0];
                if ($arg instanceof Arg && $arg->value instanceof String_) {
                    return $arg->value->value === 'system.performance';
                }
            }
        }

        return false;
    }
}
