<?php

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated entity_create() calls.
 *
 * See https://www.drupal.org/node/2266845 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 * - Using class ::create() methods like Node::create().
 */
final class EntityDeleteMultipleRector extends AbstractRector
{

    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated entity_delete_multiple() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
entity_delete_multiple('node', [1, 2, 42]);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::service('entity_type.manager')->getStorage('node')->delete(\Drupal::service('entity_type.manager')->getStorage('node')->loadMultiple(1, 2, 42));
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
        if ($this->getName($node->name) === 'entity_delete_multiple') {
            $service = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'service', [new Node\Arg(new Node\Scalar\String_('entity_type.manager'))]);

            $getStorage_method_name = new Node\Identifier('getStorage');

            $entity_type = $node->args[0];

            $getStorage_node = new Node\Expr\MethodCall($service, $getStorage_method_name, [$entity_type]);

            $create_method_load_multiple = new Node\Identifier('loadMultiple');
            $create_method_delete = new Node\Identifier('delete');

            // Set the default argument to an empty array.
            $entity_values = new Node\Arg(new Node\Expr\Array_());

            // If we have values for the new entity, pass them to the create method.
            if (count($node->args) === 2) {
                $entity_values = $node->args[1];
            }

            $node_load_multiple = new Node\Expr\MethodCall($getStorage_node, $create_method_load_multiple, [$entity_values]);
            return new Node\Expr\MethodCall($getStorage_node, $create_method_delete, [new Node\Arg($node_load_multiple)]);
        }

        return null;
    }
}
