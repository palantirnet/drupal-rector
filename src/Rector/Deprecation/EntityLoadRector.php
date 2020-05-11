<?php

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaced deprecated entity_load() calls.
 *
 * See https://www.drupal.org/node/2266845 for change record.
 *
 * What is covered:
 * - Static replacement
 * - The reset parameter is handled with a ternary operator
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class EntityLoadRector extends AbstractRector
{

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated FILE_CREATE_DIRECTORY use',[
            new CodeSample(
                <<<'CODE_BEFORE'
$node = entity_load('node', 123);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$node = \Drupal::entityManager()->getStorage('node')->load(123);
CODE_AFTER
            )
        ]);
    }

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
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Expr\FuncCall $node */
        if ($this->getName($node->name) === 'entity_load') {
            /* @var Node\Arg $entity_type. */
            $entity_type = $node->args[0];

            /* @var Node\Arg $entity_id. */
            $entity_id = $node->args[1];

            $name = new Node\Name\FullyQualified('Drupal');

            $call = new Node\Identifier('service');

            $entity_type_manager_args = [
                new Node\Arg(new Node\Scalar\String_('entity_type.manager')),
            ];

            $var = new Node\Expr\StaticCall($name, $call, $entity_type_manager_args);

            $getStorage_method_name = new Node\Identifier('getStorage');

            // Start to build the new node.
            $get_storage_node = new Node\Expr\MethodCall($var, $getStorage_method_name, [$entity_type]);

            // Create the simple version of the entity load.
            $load_method_name = new Node\Identifier('load');

            $new_node = new Node\Expr\MethodCall($get_storage_node, $load_method_name, [$entity_id]);

            // We need to account for the `reset` option which adds a method to the chain.
            // We will replace the original method with a ternary to evaluate and provide both options.
            if (count($node->args) == 3) {
                /* @var Node\Arg $reset_flag. */
                $reset_flag = $node->args[2];

                $resetCache_method_name = new Node\Identifier('resetCache');

                $reset_args = [
                    // This creates a new argument that wraps the entity ID in an array.
                    new Node\Arg(new Node\Expr\Array_([new Node\Expr\ArrayItem($entity_id->value)])),
                ];

                // TODO: Create ternary.
                $entity_load_reset_node = new Node\Expr\MethodCall($get_storage_node, $resetCache_method_name, $reset_args);

                $entity_load_reset_node = new Node\Expr\MethodCall($entity_load_reset_node, $load_method_name, [$entity_id]);

                // Replace the new_node with a ternary.
                $new_node = new Node\Expr\Ternary($reset_flag->value, $entity_load_reset_node, $new_node);
            }

            return $new_node;
        }

        return null;
    }
}
