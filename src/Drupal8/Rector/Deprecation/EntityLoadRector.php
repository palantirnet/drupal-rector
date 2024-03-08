<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\Deprecation;

use DrupalRector\Drupal8\Rector\ValueObject\EntityLoadConfiguration;
use DrupalRector\Services\AddCommentService;
use PhpParser\Node;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaced deprecated entity_load() and ENTITY_TYPE_load() calls.
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
final class EntityLoadRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var \DrupalRector\Drupal8\Rector\ValueObject\EntityLoadConfiguration[]
     */
    protected array $entityTypes;

    /**
     * @var AddCommentService
     */
    private AddCommentService $commentService;

    public function __construct(AddCommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated ENTITY_TYPE_load() or entity_load() use', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$entity = ENTITY_TYPE_load(123);
$node = entity_load('node', 123);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$entity = \Drupal::entityManager()->getStorage('ENTITY_TYPE')->load(123);
$node = \Drupal::entityManager()->getStorage('node')->load(123);
CODE_AFTER
                ,
                [
                    new EntityLoadConfiguration('entity'),
                    new EntityLoadConfiguration('file'),
                    new EntityLoadConfiguration('node'),
                    new EntityLoadConfiguration('user'),
                ]
            ),
        ]);
    }

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof EntityLoadConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', EntityLoadConfiguration::class));
            }
        }

        $this->entityTypes = $configuration;
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

        foreach ($this->entityTypes as $entityTypeConfig) {
            $entityType = $entityTypeConfig->getEntityType();

            $is_rector_rule_entity_load = $entityType === 'entity';

            if ($is_rector_rule_entity_load) {
                $method_name = 'entity_load';
            } else {
                // This will work for node_load, etc.
                $method_name = $entityType.'_load';
            }

            if (!$node->expr instanceof Node\Expr\Assign) {
                return null;
            }
            if (!$node->expr->expr instanceof Node\Expr\FuncCall) {
                return null;
            }
            $expr = $node->expr->expr;

            if ($this->getName($expr->name) === $method_name) {
                // We are doing this here, because we know we have access to arguments since we have already checked the method name.
                if ($is_rector_rule_entity_load) {
                    // Since we have one more argument, all the array keys are one greater.
                    $argument_offset = 1;

                    /* @var Node\Arg $entity_type . */
                    $entity_type = $expr->args[0];
                } // If we do not set entityType, we are using entity_load().
                else {
                    $argument_offset = 0;

                    // Create an argument, like that which is passed by entity_load().
                    $entity_type = new Node\Arg(new Node\Scalar\String_($entityType));
                }

                /* @var Node\Arg $entity_id . */
                $entity_id = $expr->args[0 + $argument_offset];

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
                if (count($expr->args) == (2 + $argument_offset)) {
                    $this->commentService->addDrupalRectorComment($node,
                        'A ternary operator is used here to keep the conditional contained within this part of the expression. Consider wrapping this statement in an `if / else` statement.');

                    /* @var Node\Arg $reset_flag . */
                    $reset_flag = $expr->args[1 + $argument_offset];

                    $resetCache_method_name = new Node\Identifier('resetCache');

                    if (!class_exists('\PhpParser\Node\ArrayItem')) {
                        $arrayItems = [new Node\Expr\ArrayItem($entity_id->value)];
                    } else {
                        $arrayItems = [new Node\ArrayItem($entity_id->value)];
                    }

                    $reset_args = [
                        // This creates a new argument that wraps the entity ID in an array.
                        new Node\Arg(new Node\Expr\Array_($arrayItems)),
                    ];

                    $entity_load_reset_node = new Node\Expr\MethodCall($getStorage_node,
                        $resetCache_method_name, $reset_args);

                    $entity_load_reset_node = new Node\Expr\MethodCall($entity_load_reset_node,
                        $load_method_name, [$entity_id]);

                    // Replace the new_node with a ternary.
                    $new_node = new Node\Expr\Ternary($reset_flag->value,
                        $entity_load_reset_node, $new_node);
                }

                $node->expr->expr = $new_node;

                return $node;
            }
        }

        return null;
    }
}
