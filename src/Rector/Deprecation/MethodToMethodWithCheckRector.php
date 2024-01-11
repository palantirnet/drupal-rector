<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use DrupalRector\Services\AddCommentService;
use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Exception\ShouldNotHappenException;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated method calls with a new method.
 *
 * What is covered:
 * - Changes the name of the method.
 *
 * Improvement opportunities:
 * - Checks the variable has a certain class.
 */
class MethodToMethodWithCheckRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var MethodToMethodWithCheckConfiguration[]
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
            if (!($value instanceof MethodToMethodWithCheckConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', MethodToMethodWithCheckConfiguration::class));
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
            Node\Expr\MethodCall::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Stmt\Expression || $node instanceof Node\Expr\MethodCall);

        if (!$node instanceof Node\Expr\MethodCall && !$node->expr instanceof Node\Expr\MethodCall && !($node->expr instanceof Node\Expr\Assign && $node->expr->expr instanceof Node\Expr\MethodCall)) {
            return null;
        }

        foreach ($this->configuration as $configuration) {
            if ($node instanceof Node\Expr\MethodCall && $this->getName($node->name) !== $configuration->getDeprecatedMethodName()) {
                continue;
            }

            if ($node instanceof Node\Stmt\Expression && $node->expr instanceof Node\Expr\MethodCall && $this->getName($node->expr->name) !== $configuration->getDeprecatedMethodName()) {
                continue;
            }

            if ($node instanceof Node\Stmt\Expression && $node->expr instanceof Node\Expr\Assign && $node->expr->expr instanceof Node\Expr\MethodCall && $this->getName($node->expr->expr->name) !== $configuration->getDeprecatedMethodName()) {
                continue;
            }

            if ($node instanceof Node\Expr\MethodCall) {
                $methodNode = $this->refactorNode($node, null, $configuration);
                if (is_null($methodNode)) {
                    continue;
                }

                return $methodNode;
            }

            if ($node->expr instanceof Node\Expr\MethodCall) {
                $methodNode = $this->refactorNode($node->expr, $node, $configuration);
                if (is_null($methodNode)) {
                    continue;
                }
                $node->expr = $methodNode;
            } elseif ($node->expr instanceof Node\Expr\Assign && $node->expr->expr instanceof Node\Expr\MethodCall) {
                $methodNode = $this->refactorNode($node->expr->expr, $node, $configuration);
                if (is_null($methodNode)) {
                    continue;
                }
                $node->expr->expr = $methodNode;
            }

            return $node;
        }

        return null;
    }

    public function refactorNode(Node\Expr\MethodCall $node, ?Node\Stmt\Expression $statement, MethodToMethodWithCheckConfiguration $configuration): ?Node\Expr\MethodCall
    {
        assert($node instanceof Node\Expr\MethodCall);

        $callerType = $this->nodeTypeResolver->getType($node->var);
        $expectedType = new ObjectType($configuration->getClassName());

        $isSuperOf = $expectedType->isSuperTypeOf($callerType);
        if ($isSuperOf->yes()) {
            $node->name = new Node\Identifier($configuration->getMethodName());

            return $node;
        }

        if ($isSuperOf->maybe()) {
            if ($node->var instanceof Node\Expr\Variable) {
                $node_var = $node->var->name;
                $node_var = "$$node_var";
            } elseif ($node->var instanceof Node\Expr\MethodCall) {
                $node_var = $node->var->name;
                $node_var = "$node_var()";
            } else {
                throw new ShouldNotHappenException('Unexpected node type: '.get_class($node->var));
            }
            $className = $configuration->getClassName();

            if (!is_null($statement)) {
                $this->commentService->addDrupalRectorComment(
                    $statement,
                    "Please confirm that `$node_var` is an instance of `$className`. Only the method name and not the class name was checked for this replacement, so this may be a false positive."
                );
            }
            $node->name = new Node\Identifier($configuration->getMethodName());

            return $node;
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated MetadataBag::clearCsrfTokenSeed() calls, used in Drupal 8 and 9 deprecations', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$metadata_bag = new \Drupal\Core\Session\MetadataBag(new \Drupal\Core\Site\Settings([]));
$metadata_bag->clearCsrfTokenSeed();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$metadata_bag = new \Drupal\Core\Session\MetadataBag(new \Drupal\Core\Site\Settings([]));
$metadata_bag->stampNew();
CODE_AFTER
                ,
                [
                    new MethodToMethodWithCheckConfiguration(
                        'Drupal\Core\Session\MetadataBag',
                        'clearCsrfTokenSeed',
                        'stampNew'
                    ),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$url = $entity->urlInfo();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$url = $entity->toUrl();
CODE_AFTER
                ,
                [
                    new MethodToMethodWithCheckConfiguration('Drupal\Core\Entity\EntityInterface', 'urlInfo', 'toUrl'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
/* @var \Drupal\node\Entity\Node $node */
$node = \Drupal::entityTypeManager()->getStorage('node')->load(123);
$entity_type = $node->getEntityType();
$entity_type->getLowercaseLabel();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
/* @var \Drupal\node\Entity\Node $node */
$node = \Drupal::entityTypeManager()->getStorage('node')->load(123);
$entity_type = $node->getEntityType();
$entity_type->getSingularLabel();
CODE_AFTER
                ,
                [
                    new MethodToMethodWithCheckConfiguration('Drupal\Core\Entity\EntityTypeInterface', 'getLowercaseLabel', 'getSingularLabel'),
                ]
            ),
        ]);
    }
}
