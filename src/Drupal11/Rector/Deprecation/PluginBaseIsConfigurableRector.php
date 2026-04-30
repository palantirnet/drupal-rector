<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated PluginBase::isConfigurable() with an instanceof check.
 *
 * @see https://www.drupal.org/node/3459533
 */
final class PluginBaseIsConfigurableRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Expr\MethodCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Node\Expr\MethodCall) {
            return null;
        }

        if ($this->getName($node->name) !== 'isConfigurable') {
            return null;
        }

        if ($node->args !== []) {
            return null;
        }

        if (!$node->var instanceof Node\Expr\Variable) {
            return null;
        }

        if ($this->getName($node->var) !== 'this') {
            return null;
        }

        return new Node\Expr\Instanceof_(
            $node->var,
            new Node\Name\FullyQualified('Drupal\Component\Plugin\ConfigurableInterface')
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replaces deprecated PluginBase::isConfigurable() with instanceof ConfigurableInterface', [
            new CodeSample(
                '$this->isConfigurable()',
                '$this instanceof \Drupal\Component\Plugin\ConfigurableInterface'
            ),
        ]);
    }
}
