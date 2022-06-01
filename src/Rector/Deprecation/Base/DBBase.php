<?php

namespace DrupalRector\Rector\Deprecation\Base;

use DrupalRector\Utility\AddCommentTrait;
use PhpParser\Node;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

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
abstract class DBBase extends AbstractRector implements ConfigurableRectorInterface
{
    use AddCommentTrait;

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

    public function configure(array $configuration): void
    {
        $this->configureNoticesAsComments($configuration);
    }

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
        if ($this->getName($node->name) === $this->deprecatedMethodName) {

            // TODO: Check if we have are in a class and inject \Drupal\Core\Database\Connection

            // TODO: Check if we have are in a class and don't have access to the container, use `\Drupal\core\Database\Database::getConnection()`.

            $name = new Node\Name\FullyQualified('Drupal');
            $call = new Node\Identifier('database');

            $method_arguments = [];

            // The 'target' key in the $options can be used to use a non-default database.
            if (count($node->args) >= $this->optionsArgumentPosition) {

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
                    $this->addDrupalRectorComment($node, 'If your `options` argument contains a `target` key, you will need to use `\Drupal\core\Database\Database::getConnection(\'my_database\'). Drupal Rector could not yet evaluate the `options` argument since it was a variable.');
                }
            }
            else {
                $this->addDrupalRectorComment($node, 'You will need to use `\Drupal\core\Database\Database::getConnection()` if you do not yet have access to the container here.');
            }

            $var = new Node\Expr\StaticCall($name, $call, $method_arguments);

            $method_name = new Node\Identifier($this->getMethodName());

            $node = new Node\Expr\MethodCall($var, $method_name, $node->args);

            return $node;
        }

        return null;
    }
}
