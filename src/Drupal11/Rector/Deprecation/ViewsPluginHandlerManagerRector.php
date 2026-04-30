<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated Views::pluginManager() and Views::handlerManager() calls.
 *
 * @see https://www.drupal.org/node/3566424
 */
final class ViewsPluginHandlerManagerRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Expr\StaticCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Node\Expr\StaticCall) {
            return null;
        }

        if (!$this->isName($node->class, 'Drupal\views\Views')) {
            return null;
        }

        $methodName = $this->getName($node->name);
        if ($methodName !== 'pluginManager' && $methodName !== 'handlerManager') {
            return null;
        }

        if (count($node->args) === 0) {
            return null;
        }

        $arg = $node->args[0];
        if (!$arg instanceof Node\Arg) {
            return null;
        }

        $typeExpr = $arg->value;
        $drupalClass = new Node\Name\FullyQualified('Drupal');

        if ($typeExpr instanceof Node\Scalar\String_) {
            return new Node\Expr\StaticCall(
                $drupalClass,
                'service',
                [new Node\Arg(new Node\Scalar\String_('plugin.manager.views.'.$typeExpr->value))]
            );
        }

        $serviceLocatorCall = new Node\Expr\StaticCall(
            $drupalClass,
            'service',
            [new Node\Arg(new Node\Scalar\String_('views.plugin_managers'))]
        );

        return new Node\Expr\MethodCall($serviceLocatorCall, 'get', [new Node\Arg($typeExpr)]);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replaces deprecated Views::pluginManager() and Views::handlerManager() with \\Drupal::service() equivalents', [
            new CodeSample(
                "Views::handlerManager('filter');\nViews::pluginManager(\$type);",
                "\\Drupal::service('plugin.manager.views.filter');\n\\Drupal::service('views.plugin_managers')->get(\$type);"
            ),
        ]);
    }
}
