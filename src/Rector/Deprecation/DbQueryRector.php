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
            // DEBUGGING:
            if (array_key_exists(2, $node->args)) {
                // DAN: this returns PhpParser\Node\Expr\Variable, not an
                //  array class, as I expected.
                var_dump(get_class($node->args[2]->value));
            }
            // END DEBUGGING

            // Check to see if a target array is passed.
            if (array_key_exists(2, $node->args) and !empty($node->args[2]->items)) {
                foreach ($node->args[2]->items as $item) {
                  if ((string) $item->key === 'target') {
                    $method_arguments[] = (string) $item->value;
                  }
                }
            }
            $var = new Node\Expr\StaticCall($name, $call, $method_arguments);
            $name = new Node\Name('query');
            $node = new Node\Expr\MethodCall($var, $name, $node->args);
        }

        return $node;
    }
}
