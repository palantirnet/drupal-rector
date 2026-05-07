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
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated Node module procedural functions with their successors.
 *
 * node_type_get_names(), node_get_type_label(), and node_mass_update() are
 * deprecated in drupal:11.3.0 and removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3571623
 */
final class ReplaceNodeModuleProceduralFunctionsRector extends AbstractDrupalCoreRector
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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated Node module procedural functions with OOP equivalents',
            [
                new ConfiguredCodeSample(
                    'node_type_get_names();',
                    "\\Drupal::service('entity_type.bundle.info')->getBundleLabels('node');",
                    [new DrupalIntroducedVersionConfiguration('11.3.0')]
                ),
                new ConfiguredCodeSample(
                    'node_get_type_label($node);',
                    '$node->getBundleEntity()->label();',
                    [new DrupalIntroducedVersionConfiguration('11.3.0')]
                ),
                new ConfiguredCodeSample(
                    'node_mass_update($nids, $updates, NULL, TRUE);',
                    '\\Drupal::service(\\Drupal\\node\\NodeBulkUpdate::class)->process($nids, $updates, NULL, TRUE);',
                    [new DrupalIntroducedVersionConfiguration('11.3.0')]
                ),
            ]
        );
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
            'node_type_get_names' => $this->refactorNodeTypeGetNames(),
            'node_get_type_label' => $this->refactorNodeGetTypeLabel($node),
            'node_mass_update' => $this->refactorNodeMassUpdate($node),
            default => null,
        };
    }

    private function refactorNodeTypeGetNames(): Node
    {
        $serviceCall = new StaticCall(
            new FullyQualified('Drupal'),
            'service',
            [new Arg(new String_('entity_type.bundle.info'))]
        );

        return new MethodCall($serviceCall, 'getBundleLabels', [new Arg(new String_('node'))]);
    }

    private function refactorNodeGetTypeLabel(FuncCall $node): ?Node
    {
        if (count($node->args) < 1) {
            return null;
        }
        $nodeArg = $node->args[0]->value;

        return new MethodCall(
            new MethodCall($nodeArg, 'getBundleEntity'),
            'label'
        );
    }

    private function refactorNodeMassUpdate(FuncCall $node): ?Node
    {
        if (count($node->args) < 2) {
            return null;
        }

        $serviceCall = new StaticCall(
            new FullyQualified('Drupal'),
            'service',
            [new Arg(new ClassConstFetch(
                new FullyQualified('Drupal\node\NodeBulkUpdate'),
                'class'
            ))]
        );

        return new MethodCall($serviceCall, 'process', $node->args);
    }
}
