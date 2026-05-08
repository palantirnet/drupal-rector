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
 * Replaces deprecated filter module procedural functions with FilterFormatRepositoryInterface service methods.
 *
 * Deprecated in drupal:11.4.0 and removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/2536594
 */
class FilterFormatFunctionsToServiceRector extends AbstractDrupalCoreRector
{
    private const SERVICE_CLASS = 'Drupal\filter\FilterFormatRepositoryInterface';

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
            'filter_fallback_format' => $this->buildServiceCall('getFallbackFormatId', []),
            'filter_formats' => count($node->args) === 0
                ? $this->buildServiceCall('getAllFormats', [])
                : $this->buildServiceCall('getFormatsForAccount', [$node->args[0]->value]),
            'filter_get_roles_by_format' => count($node->args) >= 1
                ? new MethodCall($node->args[0]->value, 'getRoles')
                : null,
            'filter_get_formats_by_role' => count($node->args) >= 1
                ? $this->buildServiceCall('getFormatsByRole', [$node->args[0]->value])
                : null,
            'filter_default_format' => $this->buildDefaultFormatCall($node),
            default => null,
        };
    }

    private function buildDefaultFormatCall(FuncCall $node): MethodCall
    {
        $args = count($node->args) > 0 ? [$node->args[0]->value] : [];

        return new MethodCall(
            $this->buildServiceCall('getDefaultFormat', $args),
            'id'
        );
    }

    /** @param Node\Expr[] $argExprs */
    private function buildServiceCall(string $method, array $argExprs): MethodCall
    {
        $serviceCall = new StaticCall(
            new FullyQualified('Drupal'),
            'service',
            [new Arg(new ClassConstFetch(new FullyQualified(self::SERVICE_CLASS), 'class'))]
        );

        $args = array_map(static fn ($expr) => new Arg($expr), $argExprs);

        return new MethodCall($serviceCall, $method, $args);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated filter module procedural functions with FilterFormatRepositoryInterface service methods.',
            [
                new ConfiguredCodeSample(
                    'filter_fallback_format();',
                    '\Drupal::service(\Drupal\filter\FilterFormatRepositoryInterface::class)->getFallbackFormatId();',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    'filter_formats();',
                    '\Drupal::service(\Drupal\filter\FilterFormatRepositoryInterface::class)->getAllFormats();',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    'filter_get_roles_by_format($format);',
                    '$format->getRoles();',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }
}
