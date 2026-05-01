<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated Node module procedural functions with their successors.
 *
 * node_type_get_names(), node_get_type_label(), and node_mass_update() are
 * deprecated in drupal:11.3.0 and removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3571623
 */
final class ReplaceNodeModuleProceduralFunctionsRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated Node module procedural functions with OOP equivalents',
            [
                new CodeSample(
                    'node_type_get_names();',
                    "\\Drupal::service('entity_type.bundle.info')->getBundleLabels('node');"
                ),
                new CodeSample(
                    'node_get_type_label($node);',
                    '$node->getBundleEntity()->label();'
                ),
                new CodeSample(
                    'node_mass_update($nids, $updates, NULL, TRUE);',
                    '\\Drupal::service(\\Drupal\\node\\NodeBulkUpdate::class)->process($nids, $updates, NULL, TRUE);'
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    public function refactor(Node $node): ?Node
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
