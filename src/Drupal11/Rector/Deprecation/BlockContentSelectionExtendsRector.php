<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Reparents entity reference selection plugins for the block_content entity
 * type from DefaultSelection to BlockContentSelection.
 *
 * In Drupal 11.4 block_content_query_entity_reference_alter() was deprecated.
 * That hook is what automatically filtered non-reusable blocks out of every
 * entity reference selection targeting block_content. Plugins that extend
 * DefaultSelection directly must now extend BlockContentSelection instead,
 * which performs the reusable-block filtering in buildEntityQuery() /
 * validateReferenceableNewEntities() rather than relying on the hook.
 *
 * BREAKING: this rule lives in the opt-in DRUPAL_114_BREAKING set, not the
 * default deprecation set. BlockContentSelection
 * (Drupal\block_content\Plugin\EntityReferenceSelection\BlockContentSelection)
 * was ADDED to core in the same change as the deprecation (drupal-core
 * 7f9570dba9, 11.4.0) and does not exist on any earlier minor. Reparenting a
 * subclass onto it produces a "class not found" fatal on Drupal < 11.4, and a
 * `class X extends Y` declaration is a structural node, not an Expr → Expr
 * rewrite, so it cannot be BC-wrapped with DeprecationHelper. Consumers must
 * opt in only after committing to drop support for any minor below 11.4.
 *
 * The rewrite is gated on the EntityReferenceSelection PHP attribute carrying
 * `entity_types: ["block_content"]`, so DefaultSelection subclasses that
 * target other entity types are left untouched. The canonical core
 * BlockContentSelection (which itself extends DefaultSelection and carries the
 * same attribute) is skipped explicitly so the rule does not try to make it
 * extend itself.
 *
 * @see https://www.drupal.org/node/2987159
 * @see https://www.drupal.org/node/3521459
 */
class BlockContentSelectionExtendsRector extends AbstractRector
{
    private const DEFAULT_SELECTION_CLASS = 'Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection';

    private const BLOCK_CONTENT_SELECTION_CLASS = 'Drupal\block_content\Plugin\EntityReferenceSelection\BlockContentSelection';

    private const ENTITY_REFERENCE_SELECTION_ATTR = 'Drupal\Core\Entity\Attribute\EntityReferenceSelection';

    // The deprecation is a *runtime* @trigger_error raised inside the
    // query_entity_reference_alter hook (BlockContentHooks) when a block_content
    // entity reference selection is auto-filtered — "Automatically filtering
    // block_content entity reference selection queries to only reusable blocks
    // is deprecated in drupal:11.3.0 and is removed from drupal:12.0.0. … See
    // https://www.drupal.org/node/3521459". It is not a static @deprecated
    // annotation on any symbol the plugin references, so phpstan-deprecation-rules
    // / upgrade_status cannot flag a `class X extends DefaultSelection`
    // declaration. There is no static message to match.
    public const PHPSTAN_MESSAGES = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Entity reference selection plugins for block_content that extend DefaultSelection must extend BlockContentSelection instead, to avoid the deprecated automatic reusable-block filtering hook.',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
use Drupal\Core\Entity\Attribute\EntityReferenceSelection;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[EntityReferenceSelection(
  id: "mymodule:block_content",
  label: new TranslatableMarkup("My block content selection"),
  group: "mymodule",
  entity_types: ["block_content"],
)]
class MyBlockContentSelection extends DefaultSelection {
}
CODE_BEFORE,
                    <<<'CODE_AFTER'
use Drupal\Core\Entity\Attribute\EntityReferenceSelection;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[EntityReferenceSelection(
  id: "mymodule:block_content",
  label: new TranslatableMarkup("My block content selection"),
  group: "mymodule",
  entity_types: ["block_content"],
)]
class MyBlockContentSelection extends \Drupal\block_content\Plugin\EntityReferenceSelection\BlockContentSelection {
}
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /** @param Class_ $node */
    public function refactor(Node $node): ?Node
    {
        // Must extend DefaultSelection (resolved via the name resolver, so a
        // short alias or a fully-qualified `extends` both match).
        if ($node->extends === null) {
            return null;
        }
        if (!$this->isName($node->extends, self::DEFAULT_SELECTION_CLASS)) {
            return null;
        }

        // Skip the canonical core BlockContentSelection itself — it extends
        // DefaultSelection by design and carries the same attribute, so without
        // this guard the rule would try to make it extend itself.
        if ($this->isName($node, self::BLOCK_CONTENT_SELECTION_CLASS)) {
            return null;
        }

        // Only reparent plugins whose EntityReferenceSelection attribute targets
        // the block_content entity type.
        if (!$this->hasBlockContentEntityReferenceSelectionAttribute($node)) {
            return null;
        }

        $node->extends = new FullyQualified(self::BLOCK_CONTENT_SELECTION_CLASS);

        return $node;
    }

    private function hasBlockContentEntityReferenceSelectionAttribute(Class_ $class): bool
    {
        foreach ($class->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                $attrName = $attr->name->toString();
                // Match both the short name (resolved via use-statement) and the
                // fully-qualified attribute name.
                if ($attrName !== 'EntityReferenceSelection'
                    && $attrName !== self::ENTITY_REFERENCE_SELECTION_ATTR
                ) {
                    continue;
                }
                foreach ($attr->args as $arg) {
                    // Named argument: entity_types: [...]
                    if ($arg->name === null || $arg->name->toString() !== 'entity_types') {
                        continue;
                    }
                    if (!$arg->value instanceof Array_) {
                        continue;
                    }
                    foreach ($arg->value->items as $item) {
                        if ($item->value instanceof String_
                            && $item->value->value === 'block_content'
                        ) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}
