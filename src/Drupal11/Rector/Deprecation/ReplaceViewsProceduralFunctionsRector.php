<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated Views procedural functions with OO equivalents.
 *
 * Deprecated in drupal:11.4.0, removed in drupal:13.0.0.
 *
 * - views_view_is_enabled($view)  => $view->status()
 * - views_view_is_disabled($view) => !$view->status()
 * - views_enable_view($view)      => $view->enable()->save()
 * - views_disable_view($view)     => $view->disable()->save()
 * - views_get_view_result(...)    => \Drupal\views\Views::getViewResult(...)
 *
 * @see https://www.drupal.org/node/3572243
 */
final class ReplaceViewsProceduralFunctionsRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Expr\FuncCall::class];
    }

    public function refactor(Node $node): mixed
    {
        assert($node instanceof Node\Expr\FuncCall);

        if (!$node->name instanceof Node\Name) {
            return null;
        }

        return match ($node->name->toString()) {
            'views_view_is_enabled'  => $this->statusCall($node),
            'views_view_is_disabled' => $this->negatedStatusCall($node),
            'views_enable_view'      => $this->enableSaveCall($node),
            'views_disable_view'     => $this->disableSaveCall($node),
            'views_get_view_result'  => $this->staticGetViewResult($node),
            default                  => null,
        };
    }

    private function statusCall(Node\Expr\FuncCall $node): ?Node
    {
        if (count($node->args) < 1) {
            return null;
        }
        return new Node\Expr\MethodCall($node->args[0]->value, new Node\Identifier('status'));
    }

    private function negatedStatusCall(Node\Expr\FuncCall $node): ?Node
    {
        if (count($node->args) < 1) {
            return null;
        }
        return new Node\Expr\BooleanNot(
            new Node\Expr\MethodCall($node->args[0]->value, new Node\Identifier('status'))
        );
    }

    private function enableSaveCall(Node\Expr\FuncCall $node): ?Node
    {
        if (count($node->args) < 1) {
            return null;
        }
        $enable = new Node\Expr\MethodCall($node->args[0]->value, new Node\Identifier('enable'));
        return new Node\Expr\MethodCall($enable, new Node\Identifier('save'));
    }

    private function disableSaveCall(Node\Expr\FuncCall $node): ?Node
    {
        if (count($node->args) < 1) {
            return null;
        }
        $disable = new Node\Expr\MethodCall($node->args[0]->value, new Node\Identifier('disable'));
        return new Node\Expr\MethodCall($disable, new Node\Identifier('save'));
    }

    private function staticGetViewResult(Node\Expr\FuncCall $node): Node\Expr\StaticCall
    {
        return new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal\views\Views'),
            new Node\Identifier('getViewResult'),
            $node->args
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated Views procedural functions with OO equivalents (drupal:11.4.0)', [
            new CodeSample(
                'views_enable_view($view);',
                '$view->enable()->save();'
            ),
        ]);
    }
}
