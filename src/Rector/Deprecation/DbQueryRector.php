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
            Node\Stmt\Expression::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Expr\FuncCall $exp */
        $exp = $node->expr;
        if ($exp->name instanceof Node\Name && 'db_query' === (string) $exp->name) {
            $name = new Node\Name('Database');
            $call = new Node\Name('getConnection');
            $method_arguments = [];
            // Pass options if they exist.
            if (array_key_exists(2, $exp->args) and !empty($exp->args[2]->items)) {
                var_dump('here');
                foreach ($exp->args[2]->items as $item) {
                  if ((string) $item->key === 'target') {
                    $method_arguments[] = (string) $item->value;
                  }
                }
                // Make targets call.

            }
            $var = new Node\Expr\StaticCall($name, $call, $method_arguments);
            $name = new Node\Name('query');
            $method_call = new Node\Expr\MethodCall($var, $name, $exp->args);
            $node = new Node\Stmt\Expression($method_call);
        }

        return $node;
    }
}
