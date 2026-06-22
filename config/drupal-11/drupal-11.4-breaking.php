<?php

declare(strict_types=1);

/**
 * Drupal 11.4 "breaking" deprecation rules.
 *
 * Rules in this file rewrite code into a form that does NOT run on every
 * drupal-rector-supported minor — typically because the replacement class /
 * symbol was *introduced together with* the deprecation and does not exist on
 * older minors. They cannot be BC-wrapped (most are class renames touching
 * `use` / `extends` / `implements` / `::class`, which is a structural change,
 * not an Expr → Expr rewrite).
 *
 * This file is NOT loaded by `drupal-11.4-deprecations.php` or
 * `drupal-11-all-deprecations.php`. Consumers must opt in explicitly by
 * loading `Drupal11SetList::DRUPAL_114_BREAKING` — typically only after
 * committing to drop support for any Drupal minor below the replacement's
 * introduced version.
 */

use DrupalRector\Drupal11\Rector\Deprecation\BlockContentSelectionExtendsRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveFilterTipsLongParamRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeViewControllerRector;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3560075
    // https://www.drupal.org/node/3572239 (change record)
    // Drupal\menu_link_content\Plugin\migrate\process\LinkOptions and LinkUri
    // deprecated in drupal:11.4.0, removed in drupal:13.0.0. The replacement
    // classes were ADDED to Drupal\migrate\Plugin\migrate\process in the same
    // commit as the deprecation (drupal-core 4b7913fb19a, on 11.x only) — they
    // do not exist on any Drupal 10.x branch. Running this rule against code
    // that still needs to work on Drupal 10 will produce a "class not found"
    // fatal there.
    //
    // https://www.drupal.org/node/3581109
    // Drupal\help\Plugin\Search\HelpSearch moved out of the help module and
    // renamed to Drupal\search_help\Plugin\Search\SearchHelpSearch in the new
    // search_help core sub-module (drupal-core f55aee0362e, 11.4.0 via
    // system_update_11400()). The SearchHelpSearch class does not exist on any
    // Drupal minor below 11.4, so rewriting `use` / `::class` references to it
    // would produce a "class not found" fatal there.
    //
    // PHPSTAN_MESSAGES RenameClassRector: none. The old class was moved out of
    //   core entirely with no `class_alias` BC shim and no `@deprecated` alias
    //   left behind (verified: the `Drupal\help\Plugin\Search\HelpSearch` FQCN
    //   appears nowhere in 11.4 core), so phpstan-deprecation-rules emits no
    //   deprecation message — only a plain "class not found" once a site is on
    //   11.4. There is no message for upgrade_status to match against.
    //
    // https://www.drupal.org/node/3587564
    // https://www.drupal.org/node/3590298 (change record)
    // Drupal\node\Plugin\Search\NodeSearch moved out of the node module into
    // the new search_node core sub-module and renamed to
    // Drupal\search_node\Plugin\Search\SearchNode (drupal-core bcb6694582, on
    // 11.x only). Unlike HelpSearch above, the old NodeSearch class is NOT
    // removed in 11.4 — it survives as a deprecated subclass of SearchNode
    // (@deprecated, with a class-level @trigger_error) until removal in 12.0.0.
    // It still belongs in the breaking set: SearchNode does not exist on any
    // Drupal minor below 11.4, so rewriting `use` / `extends` / `::class`
    // references to it produces a "class not found" fatal on < 11.4, and a
    // `class X extends Y` declaration is a structural node, not an Expr → Expr
    // rewrite, so it cannot be BC-wrapped.
    //
    // PHPSTAN_MESSAGES RenameClassRector: because the NodeSearch shim is
    //   annotated `@deprecated in drupal:11.4.0` at the class level,
    //   phpstan-deprecation-rules emits "Class ... extends deprecated class
    //   Drupal\node\Plugin\Search\NodeSearch: in drupal:11.4.0 and is removed
    //   from drupal:12.0.0. Instead, use
    //   \Drupal\search_node\Plugin\Search\SearchNode." for subclasses (e.g.
    //   contrib trash's TrashNodeSearch, search_exclude's
    //   SearchExcludeNodeSearch) and "Instantiation of deprecated class ..."
    //   for direct `new` calls.
    //
    // https://www.drupal.org/node/3589630
    // https://www.drupal.org/node/3589636 (change record)
    // Drupal\node\Controller\NodeViewController deprecated in drupal:11.4.0,
    // removed in drupal:13.0.0. Use
    // Drupal\Core\Entity\Controller\EntityViewController instead.
    //
    // Unlike the migrate renames above, the replacement class exists on every
    // supported minor (EntityViewController has been the parent of
    // NodeViewController since Drupal 8), so the rewrite never fatals on a
    // missing symbol. It is in the breaking set because the rename is
    // *behaviorally* breaking, identically on every minor: rewriting
    // `extends NodeViewController` to `extends EntityViewController` drops the
    // node-specific overrides (4-service create(), the currentUser /
    // entityRepository properties, the title() callback, the node view()
    // signature). A subclass relying on them can throw an ArgumentCountError
    // (inherited 2-arg create() vs a 4-arg constructor) or call an undefined
    // title(). These need manual review, so the rule is opt-in.
    //
    // PHPSTAN_MESSAGES RenameClassRector: NodeViewController is annotated
    //   `@deprecated in drupal:11.4.0` at the class level, so
    //   phpstan-deprecation-rules emits "Class ... extends deprecated class
    //   Drupal\node\Controller\NodeViewController: ..." for subclasses and
    //   "Instantiation of deprecated class ..." for direct `new` calls. The
    //   instantiation message is carried by
    //   ReplaceNodeViewControllerRector::PHPSTAN_MESSAGES.
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\menu_link_content\Plugin\migrate\process\LinkOptions' => 'Drupal\migrate\Plugin\migrate\process\LinkOptions',
        'Drupal\menu_link_content\Plugin\migrate\process\LinkUri' => 'Drupal\migrate\Plugin\migrate\process\LinkUri',
        'Drupal\help\Plugin\Search\HelpSearch' => 'Drupal\search_help\Plugin\Search\SearchHelpSearch',
        'Drupal\node\Plugin\Search\NodeSearch' => 'Drupal\search_node\Plugin\Search\SearchNode',
        'Drupal\node\Controller\NodeViewController' => 'Drupal\Core\Entity\Controller\EntityViewController',
    ]);

    // ReplaceNodeViewControllerRector additionally trims the extra
    // $current_user / $entity_repository constructor arguments from
    // `new NodeViewController(...)`, which RenameClassRector cannot do. It
    // matches both class names, so the trim is order-independent w.r.t. the
    // RenameClassRector pass above.
    $rectorConfig->rule(ReplaceNodeViewControllerRector::class);

    // https://www.drupal.org/node/2987159
    // https://www.drupal.org/node/3521459 (change record)
    // block_content_query_entity_reference_alter() — the hook that
    // automatically filtered non-reusable blocks out of every entity reference
    // selection targeting block_content — is deprecated, removed in
    // drupal:12.0.0. Entity reference selection plugins that extend
    // Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection
    // directly must now extend
    // Drupal\block_content\Plugin\EntityReferenceSelection\BlockContentSelection
    // instead, which performs the reusable-block filtering itself.
    //
    // BlockContentSelection was ADDED to core alongside the deprecation
    // (drupal-core 7f9570dba9, on the 11.4-dev branch — a new class ships in a
    // minor, so 11.4.0, even though the runtime deprecation message text reads
    // "11.3.0"). It does not exist on any earlier minor, so reparenting a
    // subclass onto it produces a "class not found" fatal on Drupal < 11.4. A
    // `class X extends Y` declaration is a structural node, not an Expr → Expr
    // rewrite, so it cannot be BC-wrapped.
    //
    // PHPSTAN_MESSAGES BlockContentSelectionExtendsRector: none. The deprecation
    // is a runtime @trigger_error raised inside the query_entity_reference_alter
    // hook when the auto-filtering happens, not a static @deprecated annotation
    // on any symbol the plugin references, so phpstan-deprecation-rules /
    // upgrade_status cannot flag a `class X extends DefaultSelection`
    // declaration. There is no static message to match.
    $rectorConfig->rule(BlockContentSelectionExtendsRector::class);

    // https://www.drupal.org/node/3505370
    // https://www.drupal.org/node/3567879 (change record)
    // The $long parameter of FilterInterface::tips() / FilterBase::tips() was
    // deprecated in drupal:11.4.0 and is removed in drupal:12.0.0. The "filter
    // tips" long-format page goes away with it.
    //
    // RemoveFilterTipsLongParamRector strips $long from a plugin's tips()
    // override (and drops the second argument from _filter_tips() calls). This
    // is NOT BC: FilterInterface and FilterBase still declare tips($long = FALSE)
    // on every Drupal minor below 11.4, and PHP rejects an override that *drops*
    // a parameter the parent declares with a fatal at class-declaration time:
    //   "Declaration of MyFilter::tips() must be compatible with
    //    Drupal\filter\Plugin\FilterInterface::tips($long = false)".
    // (The reverse — a parent that dropped $long with a subclass that still
    // declares the optional param — is fine, which is why core can deprecate it
    // ahead of removal, but a rector that rewrites the *subclass* cannot.)
    //
    // So this rule may only be applied once the consumer's minimum supported
    // Drupal is >= 11.4. It is opt-in here and must NOT block Drupal 12
    // compatibility: on Drupal 12 the parameter is gone from the parent too, so
    // the un-rewritten subclass keeps an extra optional param — phpstan will
    // grumble but it runs. Contrib that still supports Drupal < 11.4 should not
    // run this rule (it would fatal those sites); see the rejected token_filter
    // change at https://www.drupal.org/project/token_filter/issues/3603786.
    //
    // PHPSTAN_MESSAGES RemoveFilterTipsLongParamRector: the $long parameter is
    //   annotated `@deprecated in drupal:11.4.0` (a parameter-level deprecation),
    //   for which phpstan-deprecation-rules emits no discrete message — there is
    //   no static @deprecated symbol the override references. The runtime
    //   @trigger_error fires inside core's tips() handling, not at the call site.
    //   Nothing for upgrade_status to match against.
    $rectorConfig->rule(RemoveFilterTipsLongParamRector::class);
};
