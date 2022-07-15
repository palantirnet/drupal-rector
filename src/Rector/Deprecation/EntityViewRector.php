<?php

namespace DrupalRector\Rector\Deprecation;

use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Rector\Core\Rector\AbstractRector;
use PhpParser\Node;


/**
 * Replaced deprecated entity_view() calls.
 *
 * See https://www.drupal.org/node/3033656 for change record.
 *
 * What is covered:
 * - Static replacement
 * - The reset parameter is excluded.
 *
 * Improvement opportunities
 * - Include support for cache rest parameter.
 */
final class EntityViewRector extends AbstractRector
{

    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated entity_view() use',[
            new CodeSample(
                <<<'CODE_BEFORE'
$rendered = entity_view($entity, 'default');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$rendered = \Drupal::entityTypeManager()->getViewBuilder($entity
  ->getEntityTypeId())->view($entity, 'default');
CODE_AFTER
            )
        ]);
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->getName($node->name) !== 'entity_view') {
            return NULL;
        }

        $name = new Node\Name\FullyQualified('Drupal');

        $entityTypManager = new Node\Identifier('entityTypeManager');

        $var = new Node\Expr\StaticCall($name, $entityTypManager);

        $getViewBuilder_method_name = new Node\Identifier('getViewBuilder');

        $entity_reference = $node->args[0]->value;
        $getEntityTypeId_method_name = new Node\Identifier('getEntityTypeId');

        $entityRef_type_id = new Node\Expr\MethodCall($entity_reference, $getEntityTypeId_method_name);

        $view_builder = new Node\Expr\MethodCall($var, $getViewBuilder_method_name, [new Node\Arg($entityRef_type_id)]);

        $view_method_name = new Node\Identifier('view');

        $view_args = [
            $node->args[0],
            $node->args[1],
        ];

        if (isset($node->args[2])) {
            $view_args[] = $node->args[2];
        }

        return new Node\Expr\MethodCall($view_builder, $view_method_name, $view_args);
    }

}
