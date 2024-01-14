<?php

declare(strict_types=1);

namespace DrupalRector\Rector;

use Drupal\Component\Utility\DeprecationHelper;
use DrupalRector\Contract\VersionedConfigurationInterface;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PHPStan\Reflection\MethodReflection;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;

abstract class AbstractDrupalCoreRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array|\DrupalRector\Contract\VersionedConfigurationInterface[]
     */
    protected array $configuration = [];

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof VersionedConfigurationInterface)) {
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

        $callStack = $scope->getFunctionCallStackWithParameters();
        if (count($callStack) === 0) {
            return false;
        }
        [$function, $parameter] = $callStack[0];
        if (!$function instanceof MethodReflection) {
            return false;
        }
        if ($function->getName() !== 'backwardsCompatibleCall'
            || $function->getDeclaringClass()->getName() !== DeprecationHelper::class
        ) {
            return false;
        }

        return $parameter !== null && $parameter->getName() === 'deprecatedCallable';
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

            // Create a backwards compatible call if the node is a call-like expression.
            if ($node instanceof Node\Expr\CallLike && $result instanceof Node\Expr\CallLike) {
                return $this->createBcCallOnCallLike($node, $result, $configuration->getIntroducedVersion());
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

    private function createBcCallOnCallLike(Node\Expr\CallLike $node, Node\Expr\CallLike $result, string $introducedVersion): Node\Expr\StaticCall
    {
        $clonedNode = clone $node;

        return $this->nodeFactory->createStaticCall(DeprecationHelper::class, 'backwardsCompatibleCall', [
            $this->nodeFactory->createClassConstFetch(\Drupal::class, 'VERSION'),
            $introducedVersion,
            new ArrowFunction(['expr' => $result]),
            new ArrowFunction(['expr' => $clonedNode]),
        ]);
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
        ], '.0', \Drupal::VERSION);
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
        return !(version_compare($this->installedDrupalVersion(), '10.1.0', '<') || version_compare($configuration->getIntroducedVersion(), '10.0.0', '<'));
    }
}
