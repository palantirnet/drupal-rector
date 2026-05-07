<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces removed locale config batch helper functions with their renamed successors.
 *
 * Deprecated in drupal:11.1.0, removed in drupal:12.0.0.
 *
 * - locale_config_batch_set_config_langcodes()  => locale_config_batch_update_default_config_langcodes()
 * - locale_config_batch_refresh_name()          => locale_config_batch_update_config_translations()
 *
 * @see https://www.drupal.org/node/3575254
 */
final class ReplaceLocaleConfigBatchFunctionsRector extends AbstractDrupalCoreRector
{
    private const RENAME_MAP = [
        'locale_config_batch_set_config_langcodes' => 'locale_config_batch_update_default_config_langcodes',
        'locale_config_batch_refresh_name' => 'locale_config_batch_update_config_translations',
    ];

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
        return [Node\Expr\FuncCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof Node\Expr\FuncCall);

        if (!$node->name instanceof Node\Name) {
            return null;
        }

        $name = $node->name->toString();
        if (!isset(self::RENAME_MAP[$name])) {
            return null;
        }

        $newNode = clone $node;
        $newNode->name = new Node\Name(self::RENAME_MAP[$name]);

        return $newNode;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace removed locale config batch helper functions with their renamed successors (drupal:11.1.0)', [
            new ConfiguredCodeSample(
                'locale_config_batch_set_config_langcodes($context);',
                'locale_config_batch_update_default_config_langcodes($context);',
                [new DrupalIntroducedVersionConfiguration('11.1.0')]
            ),
        ]);
    }
}
