<?php

namespace DrupalRector\Rector\Deprecation\Base;

use DrupalRector\Utility\AddCommentTrait;
use PhpParser\Node;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

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
abstract class EntityLoadBase extends AbstractRector implements ConfigurableRectorInterface
{

    use AddCommentTrait;

    /**
     * The entity type to load.
     *
     * If this is not set, we assume we need to get it passed to entity_load().
     *
     * @var string $entityType
     */
    protected $entityType;

    public function configure(array $configuration): void
    {
        $this->configureNoticesAsComments($configuration);
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
        $is_rector_rule_entity_load = is_null($this->entityType);

        if ($is_rector_rule_entity_load) {
            $method_name = 'entity_load';
        } else {
            // This will work for node_load, etc.
            $method_name = $this->entityType.'_load';
        }

        /** @var Node\Expr\FuncCall $node */
        if ($this->getName($node->name) === $method_name) {
            // We are doing this here, because we know we have access to arguments since we have already checked the method name.
            if ($is_rector_rule_entity_load) {
                // Since we have one more argument, all the array keys are one greater.
                $argument_offset = 1;

                /* @var Node\Arg $entity_type . */
                $entity_type = $node->args[0];
            } // If we do not set entityType, we are using entity_load().
            else {
                $argument_offset = 0;

                // Create an argument, like that which is passed by entity_load().
                $entity_type = new Node\Arg(new Node\Scalar\String_($this->entityType));
            }

            /* @var Node\Arg $entity_id . */
            $entity_id = $node->args[0 + $argument_offset];

            $name = new Node\Name\FullyQualified('Drupal');

            $call = new Node\Identifier('service');

            $entity_type_manager_args = [
                new Node\Arg(new Node\Scalar\String_('entity_type.manager')),
            ];

            $var = new Node\Expr\StaticCall($name, $call,
                $entity_type_manager_args);

            $getStorage_method_name = new Node\Identifier('getStorage');

            // Start to build the new node.
            $getStorage_node = new Node\Expr\MethodCall($var,
                $getStorage_method_name, [$entity_type]);

            // Create the simple version of the entity load.
            $load_method_name = new Node\Identifier('load');

            $new_node = new Node\Expr\MethodCall($getStorage_node,
                $load_method_name, [$entity_id]);

            // We need to account for the `reset` option which adds a method to the chain.
            // We will replace the original method with a ternary to evaluate and provide both options.
            if (count($node->args) == (2 + $argument_offset)) {
                $this->addDrupalRectorComment($node,
                    'A ternary operator is used here to keep the conditional contained within this part of the expression. Consider wrapping this statement in an `if / else` statement.');

                /* @var Node\Arg $reset_flag . */
                $reset_flag = $node->args[1 + $argument_offset];

                $resetCache_method_name = new Node\Identifier('resetCache');

                $reset_args = [
                    // This creates a new argument that wraps the entity ID in an array.
                    new Node\Arg(new Node\Expr\Array_([new Node\Expr\ArrayItem($entity_id->value)])),
                ];

                $entity_load_reset_node = new Node\Expr\MethodCall($getStorage_node,
                    $resetCache_method_name, $reset_args);

                $entity_load_reset_node = new Node\Expr\MethodCall($entity_load_reset_node,
                    $load_method_name, [$entity_id]);

                // Replace the new_node with a ternary.
                $new_node = new Node\Expr\Ternary($reset_flag->value,
                    $entity_load_reset_node, $new_node);
            }

            return $new_node;
        }

        return null;
    }

}
