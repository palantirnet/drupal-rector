<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\AddCommentTrait;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeCollector\ScopeResolver\ParentClassScopeResolver;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
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
final class EntityManagerRector extends AbstractRector implements ConfigurableRectorInterface
{

    use AddCommentTrait;

    /**
     * @var ParentClassScopeResolver
     */
    protected $parentClassScopeResolver;

    public function __construct(
        ParentClassScopeResolver $parentClassScopeResolver
    ) {
        $this->parentClassScopeResolver = $parentClassScopeResolver;
    }

    public function configure(array $configuration): void
    {
        $this->configureNoticesAsComments($configuration);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\StaticCall::class,
            Node\Expr\MethodCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->getName($node->name) === 'entityManager') {
            if ($node instanceof Node\Expr\StaticCall && $this->getName($node->class) === 'Drupal') {
                $service = 'entity_type.manager';

                // If we call a method on `entityManager`, we need to check that method and we can call the correct service that the method uses.
                if ($node->hasAttribute(AttributeKey::NEXT_NODE)) {
                    $next_node = $node->getAttribute(AttributeKey::NEXT_NODE);

                    $service = $this->getServiceByMethodName($this->getName($next_node));
                } else {
                    $this->addDrupalRectorComment($node,
                        'We are assuming that we want to use the `entity_type.manager` service since no method was called here directly. Please confirm this is the case. See https://www.drupal.org/node/2549139 for more information.');
                }

                // This creates a service call like `\Drupal::service('entity_type.manager').
                // This doesn't use dependency injection, but it should work.
                $node = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'),
                    'service',
                    [new Node\Arg(new Node\Scalar\String_($service))]);

                return $node;
            }

            $scope = $node->getAttribute(AttributeKey::SCOPE);
            if ($scope instanceof Scope) {
                $parentClassName = $this->parentClassScopeResolver->resolveParentClassName($scope);
                if ($node instanceof Node\Expr\MethodCall && $parentClassName === 'Drupal\Core\Controller\ControllerBase') {
                    // If we call a method on `entityManager`, we need to check that method and we can call the correct service that the method uses.
                    $next_node = $node->getAttribute(AttributeKey::NEXT_NODE);

                    if (!is_null($next_node)) {
                        $service = $this->getServiceByMethodName($this->getName($next_node));

                        // This creates a service call like `\Drupal::service('entity_type.manager').
                        // This doesn't use dependency injection, but it should work.
                        $node = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'),
                            'service',
                            [new Node\Arg(new Node\Scalar\String_($service))]);
                    } else {
                        // If we are making a direct call to ->entityManager(), we can assume the new class will also have entityTypeManager.
                        $this->addDrupalRectorComment($node,
                            'We are assuming that we want to use the `$this->entityTypeManager` injected service since no method was called here directly. Please confirm this is the case. If another service is needed, you may need to inject that yourself. See https://www.drupal.org/node/2549139 for more information.');

                        $node = new Node\Expr\MethodCall(new Node\Expr\Variable('this'),
                            new Node\Identifier('entityTypeManager'));
                    }

                    return $node;
                }
            }
        }

        return null;
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

}
