<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\Deprecation;

use DrupalRector\Services\AddCommentService;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use Rector\Exception\ShouldNotHappenException;
use Rector\NodeCollector\ScopeResolver\ParentClassScopeResolver;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated `\Drupal::entityManager()` calls.
 * Replaces deprecated `$this->entityManager()` calls in classes that extend
 * `ControllerBase`.
 *
 * See https://www.drupal.org/node/2549139 for change record.
 *
 * What is covered:
 * - Static replacement
 * - Dependency injection when class extends `ControllerBase` and uses
 * `entityTypeManager`
 *
 * Improvement opportunities
 * - Dependency injection
 * - Dependency injection when class extends `ControllerBase` and does not use
 * `entityTypeManager`
 * - Complex use case handling when a different service is needed and the
 * method does not directly call the service
 */
final class EntityManagerRector extends AbstractRector
{
    /**
     * @var ParentClassScopeResolver
     */
    protected $parentClassScopeResolver;

    /**
     * @var AddCommentService
     */
    private AddCommentService $commentService;

    public function __construct(
        ParentClassScopeResolver $parentClassScopeResolver,
        AddCommentService $commentService
    ) {
        $this->parentClassScopeResolver = $parentClassScopeResolver;
        $this->commentService = $commentService;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated \Drupal::entityManager() calls',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
$entity_manager = \Drupal::entityManager();
CODE_BEFORE
                    ,
                    <<<'CODE_AFTER'
$entity_manager = \Drupal::entityTypeManager();
CODE_AFTER
                ),
            ]);
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

        $isStaticCall = $node->expr instanceof Node\Expr\StaticCall;
        $isMethodCall = $node->expr instanceof Node\Expr\MethodCall;
        $isAssignedMethodCall = $node->expr instanceof Node\Expr\Assign && $node->expr->expr instanceof Node\Expr\MethodCall;
        $isAssignedStaticCall = $node->expr instanceof Node\Expr\Assign && $node->expr->expr instanceof Node\Expr\StaticCall;

        if (!$isStaticCall && !$isMethodCall && !$isAssignedMethodCall && !$isAssignedStaticCall) {
            return null;
        }

        if (($isStaticCall || $isMethodCall) && $this->getName($node->expr->name) !== 'entityManager') {
            return null;
        }

        // The expression of the assign is a method call. Therefor we need to check if there is
        // entityManager somewhere up the call.
        if ($isAssignedMethodCall || $isAssignedStaticCall) {
            $expr = $this->findInstanceByNameInAssign($node->expr, Node\Expr\CallLike::class, 'entityManager');
            if (!is_null($expr)) {
                $exprRefactored = $this->refactorExpression($expr, $node);
                if (is_null($exprRefactored)) {
                    return null;
                }
                $node->expr = $this->replaceInstanceByNameInAssign($node->expr, $exprRefactored, Node\Expr\CallLike::class, 'entityManager');

                return $node;
            }

            return null;
        }

        $expr = $this->refactorExpression($node->expr, $node);
        if (is_null($expr)) {
            return null;
        }

        $node->expr = $expr;

        return $node;
    }

    /**
     * Find an instance of a class by name in an Node\Expr\Assign instance.
     *
     * @todo Decide if this should be a Trait.
     *
     * @see DrupalRector\Rector\Deprecation\Base\DBBase
     *
     * @phpstan-param class-string<Node> $class
     *
     * @param Node\Expr\Assign $assign
     * @param string           $class
     * @param string           $name
     *
     * @return Node|null
     */
    public function findInstanceByNameInAssign(Node\Expr\Assign $assign, string $class, string $name): ?Node
    {
        $node = $assign->expr;
        $depth = 0;

        // Should the expression be the class we are looking for and the name is the one we are looking for, we can return early.
        if ($node instanceof Node && $node instanceof $class && $this->getName($node->name) === $name) {
            $node->setAttribute(self::class, $depth);

            return $node;
        }

        // Find the relevant class with name in the chain.
        while (isset($node->var) && !($node->var instanceof $class && isset($node->var->name) && $this->getName($node->var->name) !== $name)) {
            $node = $node->var;
            ++$depth;

            if ($node instanceof $class && isset($node->name) && $this->getName($node->name) === $name) {
                $node->setAttribute(self::class, $depth);

                return $node;
            }
        }

        return null;
    }

    /**
     * Replace an instance of a class by name in an Node\Expr\Assign instance.
     *
     * @todo Decide if this should be a Trait.
     *
     * @see DrupalRector\Rector\Deprecation\Base\DBBase
     *
     * @param Node\Expr\Assign $assign
     * @param Node             $replacement
     * @param string           $class
     * @param string           $name
     *
     * @throws ShouldNotHappenException
     *
     * @return Node|null
     */
    public function replaceInstanceByNameInAssign(Node\Expr\Assign $assign, Node $replacement, string $class, string $name): ?Node
    {
        $node = $assign->expr;

        if ($node instanceof $class && $this->getName($node->name) === $name) {
            $assign->expr = $replacement;

            return $assign;
        }

        while (isset($node->var) && !($node->var instanceof $class && $this->getName($node->var->name) === $name)) {
            $node = $node->var;
        }
        if ($node->var instanceof $class) {
            $node->var = $replacement;

            return $assign;
        }

        throw new ShouldNotHappenException('When using replaceInstanceByNameInAssign it should always find an instance to replace');
    }

    /**
     * @param Node\Expr\StaticCall $node
     *
     * @return Node\Expr\StaticCall
     */
    public function refactorStaticCall(Node\Expr\StaticCall $node, Node\Stmt\Expression $statement): Node\Expr\StaticCall
    {
        $service = 'entity_type.manager';
        // If we call a method on `entityManager`, we need to check that method and we can call the correct service that the method uses.
        if ($statement->expr->expr instanceof Node\Expr\MethodCall) {
            $service = $this->getServiceByMethodName($this->getName($statement->expr->expr->name));
        } else {
            $this->commentService->addDrupalRectorComment($statement,
                'We are assuming that we want to use the `entity_type.manager` service since no method was called here directly. Please confirm this is the case. See https://www.drupal.org/node/2549139 for more information.');
        }

        // This creates a service call like `\Drupal::service('entity_type.manager').
        // This doesn't use dependency injection, but it should work.
        $node = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'),
            'service',
            [new Node\Arg(new Node\Scalar\String_($service))]);

        return $node;
    }

    /**
     * @param Node\Expr\MethodCall $node
     *
     * @return Node\Expr\MethodCall|Node\Expr\StaticCall
     */
    public function refactorMethodCall(Node\Expr\MethodCall $expr, Node\Stmt\Expression $statement): Node\Expr\CallLike
    {
        // If we call a method on `entityManager`, we need to check that method and we can call the correct service that the method uses.
        if ($expr->getAttribute(self::class) > 0) {
            $service = $this->getServiceByMethodName($this->getName($statement->expr->expr->name));

            // This creates a service call like `\Drupal::service('entity_type.manager').
            // This doesn't use dependency injection, but it should work.
            $expr = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'),
                'service',
                [new Node\Arg(new Node\Scalar\String_($service))]);
        } else {
            // If we are making a direct call to ->entityManager(), we can assume the new class will also have entityTypeManager.
            $this->commentService->addDrupalRectorComment($statement,
                'We are assuming that we want to use the `$this->entityTypeManager` injected service since no method was called here directly. Please confirm this is the case. If another service is needed, you may need to inject that yourself. See https://www.drupal.org/node/2549139 for more information.');

            $expr = new Node\Expr\MethodCall(new Node\Expr\Variable('this'),
                new Node\Identifier('entityTypeManager'));
        }

        return $expr;
    }

    private function getServiceByMethodName(string $method_name)
    {
        switch ($method_name) {
            case 'getEntityTypeLabels':
            case 'getEntityTypeFromClass':
                $service = 'entity_type.repository';
                break;

            case 'getAllBundleInfo':
            case 'getBundleInfo':
            case 'clearCachedBundles':
                $service = 'entity_type.bundle.info';
                break;

            case 'getAllViewModes':
            case 'getViewModes':
            case 'getAllFormModes':
            case 'getFormModes':
            case 'getViewModeOptions':
            case 'getFormModeOptions':
            case 'getViewModeOptionsByBundle':
            case 'getFormModeOptionsByBundle':
            case 'clearDisplayModeInfo':
                $service = 'entity_display.repository';
                break;

            case 'getBaseFieldDefinitions':
            case 'getFieldDefinitions':
            case 'getFieldStorageDefinitions':
            case 'getFieldMap':
            case 'setFieldMap':
            case 'getFieldMapByFieldType':
            case 'clearCachedFieldDefinitions':
            case 'useCaches':
            case 'getExtraFields':
                $service = 'entity_field.manager';
                break;

            case 'onEntityTypeCreate':
            case 'onEntityTypeUpdate':
            case 'onEntityTypeDelete':
                $service = 'entity_type.listener';
                break;

            case 'getLastInstalledDefinition':
            case 'getLastInstalledFieldStorageDefinitions':
                $service = 'entity_definition.repository';
                break;

            case 'loadEntityByUuid':
            case 'loadEntityByConfigTarget':
            case 'getTranslationFromContext':
                $service = 'entity.repository';
                break;

            case 'onBundleCreate':
            case 'onBundleRename':
            case 'onBundleDelete':
                $service = 'entity_bundle.listener';
                break;

            case 'onFieldStorageDefinitionCreate':
            case 'onFieldStorageDefinitionUpdate':
            case 'onFieldStorageDefinitionDelete':
                $service = 'field_storage_definition.listener';
                break;

            case 'onFieldDefinitionCreate':
            case 'onFieldDefinitionUpdate':
            case 'onFieldDefinitionDelete':
                $service = 'field_definition.listener';
                break;

            case 'getAccessControlHandler':
            case 'getStorage':
            case 'getViewBuilder':
            case 'getListBuilder':
            case 'getFormObject':
            case 'getRouteProviders':
            case 'hasHandler':
            case 'getHandler':
            case 'createHandlerInstance':
            case 'getDefinition':
            case 'getDefinitions':
            default:
                $service = 'entity_type.manager';
                break;
        }

        return $service;
    }

    /**
     * @param Node\Expr\MethodCall|Node\Expr\StaticCall $expr
     *
     * @return Node\Expr\MethodCall|Node\Expr\StaticCall|null
     */
    public function refactorExpression(Node\Expr $expr, Node\Stmt\Expression $statement): ?Node\Expr
    {
        if ($expr instanceof Node\Expr\StaticCall && $this->getName($expr->class) === 'Drupal') {
            $expr = $this->refactorStaticCall($expr, $statement);

            return $expr;
        }

        $scope = $expr->getAttribute(AttributeKey::SCOPE);
        if ($scope instanceof Scope) {
            $parentClassName = $this->parentClassScopeResolver->resolveParentClassName($scope);

            if ($expr instanceof Node\Expr\MethodCall && $parentClassName === 'Drupal\Core\Controller\ControllerBase') {
                $expr = $this->refactorMethodCall($expr, $statement);

                return $expr;
            }
        }

        return null;
    }
}
