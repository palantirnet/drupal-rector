<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated RecipeRunner::installModule() with installModules().
 *
 * Deprecated in drupal:11.4.0. The first argument (a single module name)
 * is wrapped in an array literal.
 *
 * @see https://www.drupal.org/node/3498026
 */
final class ReplaceRecipeRunnerInstallModuleRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated RecipeRunner::installModule() with installModules(), wrapping the module name in an array',
            [
                new CodeSample(
                    'RecipeRunner::installModule($module, $recipeConfigStorage, $context);',
                    'RecipeRunner::installModules([$module], $recipeConfigStorage, $context);'
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->name, 'installModule')) {
            return null;
        }
        if (!$node->class instanceof Name) {
            return null;
        }
        $className = $this->getName($node->class);
        if (!in_array($className, [
            'Drupal\Core\Recipe\RecipeRunner',
            'RecipeRunner',
            'static',
            'self',
        ], true)) {
            return null;
        }
        if (empty($node->args)) {
            return null;
        }
        $firstArg = $node->args[0];
        if (!$firstArg instanceof Arg) {
            return null;
        }
        $wrappedArray = new Array_([new ArrayItem($firstArg->value)]);
        $node->name = new Identifier('installModules');
        $node->args = array_merge([new Arg($wrappedArray)], array_slice($node->args, 1));

        return $node;
    }
}
