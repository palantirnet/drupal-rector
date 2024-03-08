<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\Deprecation;

use DrupalRector\Drupal8\Rector\ValueObject\DBConfiguration;
use DrupalRector\Services\AddCommentService;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

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
class DBRector extends AbstractRector implements ConfigurableRectorInterface
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
     * @var \DrupalRector\Drupal8\Rector\ValueObject\DBConfiguration[]
     */
    private array $configuration;

    /**
     * @var AddCommentService
     */
    private AddCommentService $commentService;

    public function __construct(AddCommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof DBConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DBConfiguration::class));
            }
        }

        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\Expression::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Stmt\Expression);

        $isFuncCall = $node->expr instanceof Expr\FuncCall;
        $isMethodCall = $node->expr instanceof MethodCall;
        $isAssignedFuncCall = $node->expr instanceof Expr\Assign && $node->expr->expr instanceof Expr\FuncCall;
        if (!$isFuncCall && !$isAssignedFuncCall && !$isMethodCall) {
            return null;
        }

        foreach ($this->configuration as $configuration) {
            if ($node->expr instanceof Expr\FuncCall && $this->getName($node->expr->name) !== $configuration->getDeprecatedMethodName()) {
                continue;
            }

            if ($node->expr instanceof Expr\Assign && $node->expr->expr instanceof Expr\FuncCall && $this->getName($node->expr->expr->name) !== $configuration->getDeprecatedMethodName()) {
                continue;
            }

            if ($node->expr instanceof Expr\FuncCall) {
                $methodCall = $this->getMethodCall($node->expr, $node, $configuration);
                $node->expr = $methodCall;

                return $node;
            }

            if ($node->expr instanceof Expr\Assign && $node->expr->expr instanceof Expr\FuncCall) {
                $methodCall = $this->getMethodCall($node->expr->expr, $node, $configuration);
                $node->expr->expr = $methodCall;

                return $node;
            }

            if ($node->expr instanceof MethodCall) {
                $funcCall = $this->findRootFuncCallForMethodCall($node->expr);
                if ($funcCall === null || $this->getName($funcCall->name) !== $configuration->getDeprecatedMethodName()) {
                    continue;
                }

                $methodCall = $this->getMethodCall($funcCall, $node, $configuration);
                $node->expr = $this->replaceFuncCallForMethodCall($node->expr, $methodCall);

                return $node;
            }
        }

        return null;
    }

    /**
     * Find the root function call for the method call. This helps us target db_delete when chained.
     *
     * @param MethodCall $methodCall
     *
     * @return Expr\FuncCall|null
     */
    public function findRootFuncCallForMethodCall(MethodCall $methodCall): ?Expr\FuncCall
    {
        $node = $methodCall;
        while (isset($node->var) && !($node->var instanceof Expr\FuncCall)) {
            $node = $node->var;
        }
        if ($node->var instanceof Expr\FuncCall) {
            return $node->var;
        }

        return null;
    }

    /**
     * Replaces the root function call with a method call and returns the Expression.
     *
     * @param MethodCall $expr
     * @param MethodCall $methodCall
     *
     * @return MethodCall|null
     */
    public function replaceFuncCallForMethodCall(MethodCall $expr, MethodCall $methodCall): ?MethodCall
    {
        $node = $expr;
        while (isset($node->var) && !($node->var instanceof Expr\FuncCall)) {
            $node = $node->var;
        }
        if ($node->var instanceof Expr\FuncCall) {
            $node->var = $methodCall;

            return $expr;
        }

        return null;
    }

    public function getMethodCall(Expr\FuncCall $expr, Node\Stmt\Expression $statement, DBConfiguration $configuration): MethodCall
    {
        // TODO: Check if we have are in a class and inject \Drupal\Core\Database\Connection
        // TODO: Check if we have are in a class and don't have access to the container, use `\Drupal\core\Database\Database::getConnection()`.
        $name = new Node\Name\FullyQualified('Drupal');
        $call = new Node\Identifier('database');

        $method_arguments = [];

        // The 'target' key in the $options can be used to use a non-default database.
        if (count($expr->getArgs()) >= $configuration->getOptionsArgumentPosition()) {
            /* @var Node\Arg $options . */
            $options = $expr->getArgs()[$configuration->getOptionsArgumentPosition() - 1];

            if ($options->value instanceof Expr\Array_) {
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
                        $expr->args[$configuration->getOptionsArgumentPosition() - 1] = $options;
                    }
                }
            }

            if ($options->value->getType() === 'Expr_Variable') {
                // TODO: Handle variable evaluation.
                $this->commentService->addDrupalRectorComment($statement, 'If your `options` argument contains a `target` key, you will need to use `\Drupal\core\Database\Database::getConnection(\'my_database\'). Drupal Rector could not yet evaluate the `options` argument since it was a variable.');
            }
        } else {
            $this->commentService->addDrupalRectorComment($statement, 'You will need to use `\Drupal\core\Database\Database::getConnection()` if you do not yet have access to the container here.');
        }

        $var = new Expr\StaticCall($name, $call, $method_arguments);

        $method_name = new Node\Identifier(substr($configuration->getDeprecatedMethodName(), 3));

        $methodCall = new MethodCall($var, $method_name, $expr->args);

        return $methodCall;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated db_delete() calls', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
db_delete($table, $options);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::database()->delete($table, $options);
CODE_AFTER
                ,
                [
                    new DBConfiguration('db_delete', 2),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
db_insert($table, $options);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::database()->insert($table, $options);
CODE_AFTER
                ,
                [
                    new DBConfiguration('db_insert', 2),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
db_query($query, $args, $options);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::database()->query($query, $args, $options);
CODE_AFTER
                ,
                [
                    new DBConfiguration('db_query', 3),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
db_select($table, $alias, $options);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::database()->select($table, $alias, $options);
CODE_AFTER
                ,
                [
                    new DBConfiguration('db_select', 3),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
db_update($table, $options);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::database()->update($table, $options);
CODE_AFTER
                ,
                [
                    new DBConfiguration('db_update', 2),
                ]
            ),
        ]);
    }
}
