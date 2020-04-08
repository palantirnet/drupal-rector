<?php

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;

/**
 * Base class for replacing deprecated db_*() calls.
 *
 * See https://www.drupal.org/node/2993033 for change record.
 *
 * What is covered:
 * - Static replacement using \Drupal::database() which assumes the container is available
 * - Option 'target' handling when passed in-line, used to access other databases, in which case \Drupal\core\Database\Database::getConnection($database) is used
 *
 * Improvement opportunities
 * - Handle variables used to specify the 'target' option
 *   - Example
 *     $opts = ['target' => 'default',
 *       'fetch' => \PDO::FETCH_OBJ,
 *       'return' => Database::RETURN_STATEMENT,
 *       'throw_exception' => TRUE,
 *       'allow_delimiter_in_query' => FALSE,
 *     ];
 *
 *     db_query($query, $args, $opts);
 * - Inject the database connection
 * - Use calls to Database::getConnection() if the container is not yet available
 */
abstract class DBBase extends AbstractRector
{
    /**
     * The method name, such as `db_query`.
     *
     * @var string
     */
    protected $deprecatedMethodName;

    /**
     * The position of the $options argument in the method.
     *
     * This varies depending on the method.
     *
     * @var int
     */
    protected $optionsArgumentPosition;

    /**
     * Return the name of the new method.
     *
     * Example: `db_query` will return `query`.
     */
    protected function getMethodName() {
      return substr($this->deprecatedMethodName, 3);
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
        if (!empty($node->name) && $node->name instanceof Node\Name && $this->deprecatedMethodName === (string) $node->name) {

            // TODO: Check if we have are in a class and inject \Drupal\Core\Database\Connection

            // TODO: Check if we have are in a class and don't have access to the container, use `\Drupal\core\Database\Database::getConnection()`.

            $name = new Node\Name\FullyQualified('Drupal');
            $call = new Node\Identifier('database');

            $method_arguments = [];

            // The 'target' key in the $options can be used to use a non-default database.
            if (array_key_exists($this->optionsArgumentPosition - 1, $node->args)) {
                /* @var Node\Arg $options. */
                $options = $node->args[$this->optionsArgumentPosition - 1];

                if ($options->value->getType() === 'Expr_Array') {
                    foreach ($options->value->items as $item_index => $item) {
                        if ($item->key->value === 'target') {
                          // Assume we need to get a different connection than the default.
                          $name = new Node\Name\FullyQualified('Drupal\core\Database\Database');
                          $call = new Node\Identifier('getConnection');

                          $method_arguments[] = new Node\Arg(new Node\Scalar\String_($item->value->value));

                            // Update the options.
                            $value = $options->value;
                            $items = $value->items;
                            unset($items[$item_index]);
                            $value->items = $items;
                            $options->value = $value;
                            $node->args[$this->optionsArgumentPosition - 1] = $options;
                        }
                    }
                }

                if ($options->value->getType() === 'Expr_Variable') {
                    // TODO: Handle variable evaluation.
                }
            }

            $var = new Node\Expr\StaticCall($name, $call, $method_arguments);

            $method_name = new Node\Identifier($this->getMethodName());
            $node = new Node\Expr\MethodCall($var, $method_name, $node->args);
        }

        return $node;
    }
}