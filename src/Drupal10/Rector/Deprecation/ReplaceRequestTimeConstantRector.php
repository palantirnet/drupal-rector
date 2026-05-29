<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces the deprecated REQUEST_TIME constant with \Drupal::time()->getRequestTime().
 *
 * Deprecated in drupal:8.3.0, removed in drupal:11.0.0.
 *
 * @see https://www.drupal.org/node/3395986
 */
final class ReplaceRequestTimeConstantRector extends AbstractDrupalCoreRector
{
    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration;

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalIntroducedVersionConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalIntroducedVersionConfiguration::class));
            }
        }
        parent::configure($configuration);
    }

    public function getNodeTypes(): array
    {
        return [Node\Expr\ConstFetch::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof Node\Expr\ConstFetch);

        if (!$this->isName($node, 'REQUEST_TIME')) {
            return null;
        }

        $staticCall = new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal'),
            new Node\Identifier('time')
        );

        return new Node\Expr\MethodCall($staticCall, new Node\Identifier('getRequestTime'));
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated REQUEST_TIME constant with \\Drupal::time()->getRequestTime() (deprecated drupal:8.3.0, removed drupal:11.0.0)', [
            new ConfiguredCodeSample(
                '$cutoff = REQUEST_TIME - $lifespan;',
                '$cutoff = \\Drupal::time()->getRequestTime() - $lifespan;',
                [new DrupalIntroducedVersionConfiguration('11.0.0')]
            ),
        ]);
    }
}
