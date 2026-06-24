<?php

declare(strict_types=1);

namespace DrupalRector\Rector;

use Drupal\Component\Utility\DeprecationHelper;
use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use DrupalRector\Services\DrupalRectorSettings;
use PhpParser\Node;
use PhpParser\Node\ClosureUse;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Reflection\MethodReflection;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;

abstract class AbstractDrupalCoreRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array|VersionedConfigurationInterface[]
     */
    protected array $configuration = [];

    public function __construct(private readonly DrupalRectorSettings $drupalRectorSettings)
    {
    }

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof VersionedConfigurationInterface) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', VersionedConfigurationInterface::class));
            }
        }

        $this->configuration = $configuration;
    }

    protected function isInBackwardsCompatibleCall(Node $node): bool
    {
        if (!class_exists(DeprecationHelper::class)) {
            return false;
        }

        $scope = $node->getAttribute(AttributeKey::SCOPE);

        foreach ($scope->getFunctionCallStackWithParameters() as [$function, $parameter]) {
            if (!$function instanceof MethodReflection) {
                continue;
            }
            if ($function->getName() !== 'backwardsCompatibleCall'
                || $function->getDeclaringClass()->getName() !== DeprecationHelper::class
            ) {
                continue;
            }
            if ($parameter !== null && $parameter->getName() === 'deprecatedCallable') {
                return true;
            }
        }

        return false;
    }

    public function refactor(Node $node)
    {
        foreach ($this->configuration as $configuration) {
            if ($this->rectorShouldApplyToDrupalVersion($configuration) === false) {
                continue;
            }

            if ($this->isInBackwardsCompatibleCall($node)) {
                continue;
            }

            $result = $this->refactorWithConfiguration($node, $configuration);

            // Skip if no result.
            if ($result === null) {
                continue;
            }

            // Check if Drupal version and the introduced version support backward
            // compatible calls. Although it was introduced in Drupal 10.1 we
            // also supply these patches for changes introduced in Drupal 10.0.
            // The reason for this is that will start supplying patches for
            // Drupal 10 when 10.0 is already out of support. This means that
            // we will not support running drupal-rector on Drupal 10.0.x.
            if ($this->supportBackwardsCompatibility($configuration) === false) {
                return $result;
            }

            if ($node instanceof Node\Expr && $result instanceof Node\Expr) {
                return $this->createBcCallOnExpr($node, $result, $configuration);
            }

            return $result;
        }

        return null;
    }

    /**
     * Process Node of matched type.
     *
     * @return Node|Node[]|null
     */
    abstract protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration);

    /**
     * @param VersionedConfigurationInterface|string $configuration the matched
     *                                                              configuration, or — for callers that replace constants and have no call
     *                                                              arguments to worry about — the introduced version string directly
     */
    protected function createBcCallOnExpr(Node\Expr $node, Node\Expr $result, $configuration): Node\Expr\StaticCall
    {
        $introducedVersion = $configuration instanceof VersionedConfigurationInterface
            ? $configuration->getIntroducedVersion()
            : $configuration;
        $configuration = $configuration instanceof VersionedConfigurationInterface ? $configuration : null;

        $clonedNode = clone $node;

        // DeprecationHelper::backwardsCompatibleCall() invokes the callables with
        // no arguments, so they have to close over the call's arguments. An arrow
        // function captures by value, which silently drops any mutation made
        // through a by-reference parameter (e.g. template_preprocess_html(&$variables)).
        // When the call passes a local variable to a by-reference parameter we
        // therefore emit a long closure that captures that variable by reference;
        // otherwise we keep the cleaner arrow function.
        $byRefVariables = $this->collectByReferenceArgumentVariables($node, $configuration);

        if ($byRefVariables === []) {
            $newCallable = new ArrowFunction(['expr' => $result]);
            $oldCallable = new ArrowFunction(['expr' => $clonedNode]);
        } else {
            // PHPStan flags `return <void-expr>;` (function.void) at every level,
            // but allows the same call as a bare expression statement — exactly
            // what an arrow function's implicit return is treated as. Only return
            // a value when the replacement method actually produces one.
            $returnsValue = !$this->serviceMethodReturnsVoid($configuration);
            $newCallable = $this->createByReferenceClosure($result, $byRefVariables, $returnsValue);
            $oldCallable = $this->createByReferenceClosure($clonedNode, $byRefVariables, $returnsValue);
        }

        return $this->nodeFactory->createStaticCall(DeprecationHelper::class, 'backwardsCompatibleCall', [
            $this->nodeFactory->createClassConstFetch(\Drupal::class, 'VERSION'),
            $introducedVersion,
            $newCallable,
            $oldCallable,
        ]);
    }

    /**
     * Builds a `function () use (&$a, &$b) { <expr>; }` closure.
     *
     * When $returnsValue is true the body is `return <expr>;`, otherwise it is
     * the bare expression statement `<expr>;` so PHPStan does not flag the use
     * of a void result (function.void).
     *
     * @param string[] $variableNames
     */
    private function createByReferenceClosure(Node\Expr $expr, array $variableNames, bool $returnsValue): Closure
    {
        $uses = array_map(
            static fn (string $name): ClosureUse => new ClosureUse(new Node\Expr\Variable($name), true),
            $variableNames
        );

        $statement = $returnsValue ? new Return_($expr) : new Expression($expr);

        return new Closure([
            'uses' => $uses,
            'stmts' => [$statement],
        ]);
    }

    /**
     * Whether the replacement service method is declared to return void.
     *
     * A long closure is only ever emitted once a real, reflectable class method
     * has been found (by-reference detection requires it), so the return type is
     * reliably available here. Anything other than an explicit `void` return
     * type — including an undeclared return type — keeps the `return` so a real
     * value is never silently dropped; PHPStan only flags function.void when it
     * actually knows the call is void.
     */
    private function serviceMethodReturnsVoid(?VersionedConfigurationInterface $configuration): bool
    {
        if (!$configuration instanceof FunctionToServiceConfiguration) {
            return false;
        }

        $className = $configuration->getServiceName();
        $methodName = $configuration->getServiceMethodName();
        if (!class_exists($className) || !method_exists($className, $methodName)) {
            return false;
        }

        try {
            $returnType = (new \ReflectionMethod($className, $methodName))->getReturnType();
        } catch (\ReflectionException) {
            return false;
        }

        return $returnType instanceof \ReflectionNamedType && $returnType->getName() === 'void';
    }

    /**
     * Returns the local variables passed to a by-reference parameter of the call.
     *
     * The by-reference parameters are collected from both the original
     * (deprecated) callable and the replacement callable, so a mutation is
     * preserved whichever branch DeprecationHelper picks. Capturing a variable
     * that is actually consumed by value is harmless: the closure body is a
     * single expression and never reassigns the variable.
     *
     * @return string[] the names of the local variables to capture by reference
     */
    private function collectByReferenceArgumentVariables(Node\Expr $node, ?VersionedConfigurationInterface $configuration): array
    {
        if (!$node instanceof Node\Expr\CallLike) {
            return [];
        }

        $byRefPositions = [];

        // Replacement service method: when the target is a fully-qualified class
        // name (rather than a container service id) it is a real class, reliably
        // reflectable. reflectByReferenceParameterPositions() guards class_exists(),
        // so service ids such as 'file.repository' just yield nothing here.
        if ($configuration instanceof FunctionToServiceConfiguration) {
            foreach ($this->reflectByReferenceParameterPositions($configuration->getServiceName(), $configuration->getServiceMethodName()) as $position) {
                $byRefPositions[$position] = true;
            }
        }

        // Original deprecated function: best-effort, procedural functions are
        // frequently not autoloadable during a Rector run.
        if ($node instanceof Node\Expr\FuncCall && $node->name instanceof Node\Name) {
            $functionName = $node->name->toString();
            if (function_exists($functionName)) {
                try {
                    foreach ((new \ReflectionFunction($functionName))->getParameters() as $position => $parameter) {
                        if ($parameter->isPassedByReference()) {
                            $byRefPositions[$position] = true;
                        }
                    }
                } catch (\ReflectionException) {
                    // Ignore: fall back to whatever the replacement told us.
                }
            }
        }

        if ($byRefPositions === []) {
            return [];
        }

        $variableNames = [];
        foreach ($node->getArgs() as $position => $arg) {
            if (!isset($byRefPositions[$position])) {
                continue;
            }
            $rootVariable = $this->resolveRootVariableName($arg->value);
            if ($rootVariable !== null) {
                $variableNames[$rootVariable] = $rootVariable;
            }
        }

        return array_values($variableNames);
    }

    /**
     * @return int[] zero-based positions of by-reference parameters
     */
    private function reflectByReferenceParameterPositions(string $className, string $methodName): array
    {
        if (!class_exists($className) || !method_exists($className, $methodName)) {
            return [];
        }

        try {
            $method = new \ReflectionMethod($className, $methodName);
        } catch (\ReflectionException) {
            return [];
        }

        $positions = [];
        foreach ($method->getParameters() as $position => $parameter) {
            if ($parameter->isPassedByReference()) {
                $positions[] = $position;
            }
        }

        return $positions;
    }

    /**
     * Returns the name of the local variable an argument is rooted at.
     *
     * `$variables`, `$variables['x']` and `$obj->prop` (rooted at `$obj`) all
     * resolve to that local variable. `$this->prop` returns null: the object is
     * shared with the closure regardless of capture, so an arrow function would
     * preserve the mutation anyway.
     */
    private function resolveRootVariableName(Node\Expr $expr): ?string
    {
        while (
            $expr instanceof Node\Expr\ArrayDimFetch
            || $expr instanceof Node\Expr\PropertyFetch
            || $expr instanceof Node\Expr\NullsafePropertyFetch
        ) {
            $expr = $expr->var;
        }

        if ($expr instanceof Node\Expr\Variable && is_string($expr->name) && $expr->name !== 'this') {
            return $expr->name;
        }

        return null;
    }

    /**
     * @param VersionedConfigurationInterface $configuration
     *
     * @return bool|int
     */
    public function rectorShouldApplyToDrupalVersion(VersionedConfigurationInterface $configuration)
    {
        return version_compare($this->installedDrupalVersion(), $configuration->getIntroducedVersion(), '>=');
    }

    /**
     * @phpstan-return non-empty-string
     */
    public function installedDrupalVersion(): string
    {
        return str_replace([
            '.x-dev',
            '-dev',
        ], '.0', $this->drupalRectorSettings->getDrupalVersion() ?? \Drupal::VERSION);
    }

    /**
     * Check if Drupal version and the introduced version support backward
     * compatible calls. Although it was introduced in Drupal 10.1 we
     * also supply these patches for changes introduced in Drupal 10.0.
     * The reason for this is that will start supplying patches for
     * Drupal 10 when 10.0 is already out of support. This means that
     * we will not support running drupal-rector on Drupal 10.0.x.
     *
     * @param VersionedConfigurationInterface $configuration
     *
     * @return bool
     */
    public function supportBackwardsCompatibility(VersionedConfigurationInterface $configuration): bool
    {
        if (!$this->drupalRectorSettings->isBackwardCompatibilityEnabled()) {
            return false;
        }

        $minimumVersion = $this->drupalRectorSettings->getMinimumCoreVersionSupported();

        return !(version_compare($minimumVersion, '10.1.0', '<') || version_compare($configuration->getIntroducedVersion(), '10.0.0', '<') || version_compare($minimumVersion, $configuration->getIntroducedVersion(), '>='));
    }
}
