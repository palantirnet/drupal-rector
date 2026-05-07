<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated node_access_view_all_nodes() and its drupal_static_reset() call.
 *
 * Deprecated in drupal:11.3.0, removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3038908
 */
final class ReplaceNodeAccessViewAllNodesRector extends AbstractDrupalCoreRector
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
        return [FuncCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof FuncCall);

        if ($this->isName($node, 'node_access_view_all_nodes')) {
            return $this->buildCheckAllGrants($node);
        }

        if ($this->isName($node, 'drupal_static_reset')) {
            return $this->refactorStaticReset($node);
        }

        return null;
    }

    private function buildCheckAllGrants(FuncCall $node): MethodCall
    {
        $entityTypeManager = $this->nodeFactory->createStaticCall('Drupal', 'entityTypeManager');
        $getHandler = $this->nodeFactory->createMethodCall(
            $entityTypeManager,
            'getAccessControlHandler',
            [new String_('node')]
        );

        if (!empty($node->args) && $node->args[0] instanceof Arg) {
            $accountArg = $node->args[0]->value;
        } else {
            $accountArg = $this->nodeFactory->createStaticCall('Drupal', 'currentUser');
        }

        return $this->nodeFactory->createMethodCall($getHandler, 'checkAllGrants', [$accountArg]);
    }

    private function refactorStaticReset(FuncCall $node): ?MethodCall
    {
        if (empty($node->args) || !$node->args[0] instanceof Arg) {
            return null;
        }

        $firstArg = $node->args[0]->value;
        if (!$firstArg instanceof String_ || $firstArg->value !== 'node_access_view_all_nodes') {
            return null;
        }

        $service = $this->nodeFactory->createStaticCall(
            'Drupal',
            'service',
            [new String_('node.view_all_nodes_memory_cache')]
        );

        return $this->nodeFactory->createMethodCall($service, 'deleteAll');
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated node_access_view_all_nodes() with entityTypeManager()->getAccessControlHandler(\'node\')->checkAllGrants() (drupal:11.3.0)', [
            new ConfiguredCodeSample(
                'node_access_view_all_nodes();',
                "\\Drupal::entityTypeManager()->getAccessControlHandler('node')->checkAllGrants(\\Drupal::currentUser());",
                [new DrupalIntroducedVersionConfiguration('11.3.0')]
            ),
            new ConfiguredCodeSample(
                "drupal_static_reset('node_access_view_all_nodes');",
                "\\Drupal::service('node.view_all_nodes_memory_cache')->deleteAll();",
                [new DrupalIntroducedVersionConfiguration('11.3.0')]
            ),
        ]);
    }
}
