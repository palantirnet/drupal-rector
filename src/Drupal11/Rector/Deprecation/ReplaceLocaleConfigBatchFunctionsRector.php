<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
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
final class ReplaceLocaleConfigBatchFunctionsRector extends AbstractRector
{
    private const RENAME_MAP = [
        'locale_config_batch_set_config_langcodes' => 'locale_config_batch_update_default_config_langcodes',
        'locale_config_batch_refresh_name' => 'locale_config_batch_update_config_translations',
    ];

    public function getNodeTypes(): array
    {
        return [Node\Expr\FuncCall::class];
    }

    public function refactor(Node $node): mixed
    {
        assert($node instanceof Node\Expr\FuncCall);

        if (!$node->name instanceof Node\Name) {
            return null;
        }

        $name = $node->name->toString();
        if (!isset(self::RENAME_MAP[$name])) {
            return null;
        }

        $node->name = new Node\Name(self::RENAME_MAP[$name]);

        return $node;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace removed locale config batch helper functions with their renamed successors (drupal:11.1.0)', [
            new CodeSample(
                'locale_config_batch_set_config_langcodes($context);',
                'locale_config_batch_update_default_config_langcodes($context);'
            ),
        ]);
    }
}
