<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\Deprecation;

use DrupalRector\Drupal8\Rector\ValueObject\GetMockConfiguration;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\NodeCollector\ScopeResolver\ParentClassScopeResolver;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated getMock() calls in classes.
 *
 * See https://www.drupal.org/node/2907725 for change record.
 *
 * What is covered:
 * - Checks the class being extended.
 */
class GetMockRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var ParentClassScopeResolver
     */
    protected ParentClassScopeResolver $parentClassScopeResolver;

    /**
     * @var GetMockConfiguration[]
     */
    private array $configuration;

    public function __construct(ParentClassScopeResolver $parentClassScopeResolver)
    {
        $this->parentClassScopeResolver = $parentClassScopeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\MethodCall::class,
        ];
    }

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof GetMockConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', GetMockConfiguration::class));
            }
        }

        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\MethodCall);

        $scope = $node->getAttribute(AttributeKey::SCOPE);
        if (!$scope instanceof Scope) {
            return null;
        }

        $parentClassName = $this->parentClassScopeResolver->resolveParentClassName($scope);
        // This checks for a method call with the method name of `getMock` and that
        // the variable calling `getMock` is `$this`, not some other variable call,
        // such as `$myOtherService->getMock` and have unintended consequences.
        if ($this->getName($node->name) !== 'getMock'
            || !($node->var instanceof Node\Expr\Variable)
            || $this->getName($node->var) !== 'this'
        ) {
            return null;
        }

        foreach ($this->configuration as $configuration) {
            if ($parentClassName !== $configuration->getFullyQualifiedClassName()) {
                continue;
            }

            // Build the arguments.
            $method_arguments = $node->args;

            // Get the updated method name.
            $method_name = new Node\Identifier('createMock');

            return new Node\Expr\MethodCall($node->var, $method_name, $method_arguments);
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated getMock() calls', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->entityTypeManager = $this->getMock(EntityTypeManagerInterface::class);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
CODE_AFTER
                ,
                [
                    new GetMockConfiguration('Drupal\Tests\BrowserTestBase'),
                    new GetMockConfiguration('Drupal\KernelTests\KernelTestBase'),
                    new GetMockConfiguration('Drupal\Tests\UnitTestCase'),
                ]
            ),
        ]);
    }
}
