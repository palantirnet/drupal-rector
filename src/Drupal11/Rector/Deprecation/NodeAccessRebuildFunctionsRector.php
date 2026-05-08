<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated node_access_rebuild() and node_access_needs_rebuild() with the NodeAccessRebuild service.
 *
 * Deprecated in drupal:11.4.0 and removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3533299
 * @see https://www.drupal.org/node/3575096
 */
class NodeAccessRebuildFunctionsRector extends AbstractDrupalCoreRector
{
    /** @var DrupalIntroducedVersionConfiguration[] */
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

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof FuncCall);

        if (!$node->name instanceof Name) {
            return null;
        }

        return match ($node->name->toString()) {
            'node_access_rebuild' => $this->buildServiceCall('rebuild', $node->args),
            'node_access_needs_rebuild' => count($node->args) === 0
                ? $this->buildServiceCall('needsRebuild', [])
                : $this->buildServiceCall('setNeedsRebuild', $node->args),
            default => null,
        };
    }

    /** @param Arg[] $args */
    private function buildServiceCall(string $method, array $args): MethodCall
    {
        $serviceCall = new StaticCall(
            new FullyQualified('Drupal'),
            'service',
            [new Arg(new ClassConstFetch(new FullyQualified('Drupal\node\NodeAccessRebuild'), 'class'))]
        );

        return new MethodCall($serviceCall, $method, $args);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated node_access_rebuild() and node_access_needs_rebuild() with the NodeAccessRebuild service.',
            [
                new ConfiguredCodeSample(
                    'node_access_rebuild();',
                    '\Drupal::service(\Drupal\node\NodeAccessRebuild::class)->rebuild();',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    'node_access_needs_rebuild();',
                    '\Drupal::service(\Drupal\node\NodeAccessRebuild::class)->needsRebuild();',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    'node_access_needs_rebuild(TRUE);',
                    '\Drupal::service(\Drupal\node\NodeAccessRebuild::class)->setNeedsRebuild(TRUE);',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }
}
