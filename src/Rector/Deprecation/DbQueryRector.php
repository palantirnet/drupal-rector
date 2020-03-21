<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\TraitsByClassHelperTrait;
use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated db_query() calls.
 *
 * See https://www.drupal.org/node/2993033 for change record.
 */
final class DbQueryRector extends AbstractRector
{
    use TraitsByClassHelperTrait;

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated db_query() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
db_query($query, $args, $options);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
Database::getConnection(_db_get_target($options))->query($query, $args, $options);
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
        if (!empty($node->name) && $node->name instanceof Node\Name && 'db_query' === (string) $node->name) {
            $name = new Node\Name('Database');
            $call = new Node\Name('getConnection');
            $method_arguments = [];

            if (array_key_exists(2, $node->args)) {
                /* @var Node\Arg $options. */
                $options = $node->args[2];

                $options->getType();

                if ($options->value->getType() === 'Expr_Array') {
                    foreach ($options->value->items as $item_index => $item) {
                        if ($item->key->value === 'target') {
                            $method_arguments[] = new Node\Arg(new Node\Scalar\String_($item->value->value));

                            // Update the options.
                            $value = $options->value;
                            $items = $value->items;
                            unset($items[$item_index]);
                            $value->items = $items;
                            $options->value = $value;
                            $node->args['2'] = $options;
                        }
                    }
                }

                if ($options->value->getType() === 'Expr_Variable') {
                    // TODO: Handle variable evaluation.
                }
            }

            $var = new Node\Expr\StaticCall($name, $call, $method_arguments);
            $name = new Node\Name('query');
            $node = new Node\Expr\MethodCall($var, $name, $node->args);
        }

        return $node;
    }
}
