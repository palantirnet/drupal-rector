<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes the deprecated source_module named argument from
 * #[MigrateSource] attribute usages.
 *
 * The source_module constructor parameter was removed from
 * Drupal\migrate\Attribute\MigrateSource in drupal:11.2.0; passing
 * #[MigrateSource(source_module: '...')] now raises an "Unknown named
 * parameter" error at plugin discovery time on Drupal 11.2.0 and later.
 *
 * This is a NON-backward-compatible rewrite. An Attribute is not an
 * Expr → Expr transformation, so it cannot be BC-wrapped with
 * DeprecationHelper, and the constructor argument is mutually exclusive
 * across minors: keeping source_module fatals on 11.2.0+, while removing it
 * drops the requirement metadata that Drupal\migrate_drupal\…\DrupalSqlBase
 * relies on to enforce the source module for Drupal 6/7 migrations on older
 * Drupal. The rule therefore lives in the opt-in DRUPAL_112_BREAKING set, not
 * the default deprecation set. Apply it only after dropping support for the
 * Drupal minors that predate 11.2.0.
 *
 * Caveat: for plugins extending DrupalSqlBase the source_module value must
 * still be declared somewhere after this rule removes it from the attribute —
 * either via the @MigrateSource annotation or the source_module key in the
 * migration YAML. That re-declaration is not automated here and remains a
 * manual follow-up.
 *
 * @see https://www.drupal.org/node/3009349
 * @see https://www.drupal.org/node/3306373
 */
final class RemoveSourceModuleFromMigrateSourceAttributeRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove the source_module named argument from #[MigrateSource] attribute usages, as it was removed from the attribute class in drupal:11.2.0.',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
#[MigrateSource(
    id: 'd7_node',
    source_module: 'node',
)]
class Node extends DrupalSqlBase {}
CODE_BEFORE,
                    <<<'CODE_AFTER'
#[MigrateSource(
    id: 'd7_node',
)]
class Node extends DrupalSqlBase {}
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Attribute::class];
    }

    /** @param Attribute $node */
    public function refactor(Node $node): ?Node
    {
        // Only target \Drupal\migrate\Attribute\MigrateSource attributes.
        if ($this->getName($node->name) !== 'Drupal\\migrate\\Attribute\\MigrateSource') {
            return null;
        }

        foreach ($node->args as $key => $arg) {
            if ($arg->name !== null && $arg->name->toString() === 'source_module') {
                unset($node->args[$key]);
                // Re-index to avoid gaps.
                $node->args = array_values($node->args);

                return $node;
            }
        }

        return null;
    }
}
