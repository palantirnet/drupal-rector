<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes the deprecated boolean $has_trusted_data argument from save() calls.
 *
 * Passing any argument to Config::save() is deprecated in drupal:11.4.0 and
 * removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3347842
 */
final class RemoveConfigSaveTrustedDataArgRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove deprecated boolean $has_trusted_data argument from Config::save() calls',
            [
                new CodeSample(
                    '$config->save(TRUE);',
                    '$config->save();'
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
        if (!$this->isName($node->name, 'save')) {
            return null;
        }
        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Config\Config'))) {
            return null;
        }
        if (count($node->args) !== 1) {
            return null;
        }
        $arg = $node->args[0];
        if (!$arg instanceof Arg) {
            return null;
        }
        if (!$arg->value instanceof ConstFetch) {
            return null;
        }
        $constName = strtolower((string) $arg->value->name);
        if ($constName !== 'true' && $constName !== 'false') {
            return null;
        }
        $node->args = [];

        return $node;
    }
}
