<?php

declare(strict_types=1);

/**
 * Drupal 11.3 "breaking" deprecation rules.
 *
 * See `drupal-11.4-breaking.php` for the full contract. In short: these are
 * `RenameClassRector` entries whose replacement class does not exist on every
 * drupal-rector-supported Drupal minor. The rewrite cannot be BC-wrapped (it
 * touches `use` / `extends` / `implements` / `::class`), so running it against
 * code that still needs to work on the missing minor will fatal there.
 *
 * NOT loaded by `drupal-11.3-deprecations.php` or `drupal-11-all-deprecations.php`.
 * Opt in via `Drupal11SetList::DRUPAL_113_BREAKING`.
 */

use DrupalRector\Drupal11\Rector\Deprecation\HookRequirementsAlterRenameRector;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3490846
    // https://www.drupal.org/node/3549685 (change record)
    // hook_requirements_alter() deprecated in drupal:11.3.0, removed in
    // drupal:13.0.0. Renames procedural {module}_requirements_alter() to
    // {module}_runtime_requirements_alter(). The runtime hook is only invoked on
    // Drupal minors where it exists, so on older Drupal the renamed function is
    // never called (a silent no-op) — a non-BC rewrite. It cannot be BC-wrapped
    // (a function declaration is not an Expr → Expr transformation), hence the
    // breaking set. Apply only after dropping support for Drupal minors that
    // predate hook_runtime_requirements_alter().
    $rectorConfig->rule(HookRequirementsAlterRenameRector::class);

    // https://www.drupal.org/node/3551446
    // https://www.drupal.org/node/3551450 (change record)
    // workspaces.association service / WorkspaceAssociationInterface renamed
    // in drupal:11.3.0. Replacement WorkspaceTracker[Interface] introduced in
    // 11.3.0; does not exist on any Drupal 10.x branch.
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\workspaces\WorkspaceAssociationInterface' => 'Drupal\workspaces\WorkspaceTrackerInterface',
        'Drupal\workspaces\WorkspaceAssociation' => 'Drupal\workspaces\WorkspaceTracker',
    ]);

    // https://www.drupal.org/node/3571874
    // https://www.drupal.org/node/3527501 (change record)
    // Drupal\block_content\Access\* class aliases deprecated in drupal:11.3.0,
    // removed in drupal:12.0.0. The canonical Drupal\Core\Access\* homes were
    // added in 11.3.0; on every Drupal 10.x branch the only copy lives at
    // Drupal\block_content\Access\*, so rewriting to the Core\Access\* path
    // will fatal on D10.
    //
    // TODO PHPSTAN_MESSAGES RenameClassRector: capture against a Drupal 11.3.x
    //   test env (aliases are already gone from 11.4-dev, so live capture is
    //   not possible here). Expected shape from phpstan-deprecation-rules is
    //   either "Class MyBlock implements deprecated interface
    //   Drupal\block_content\Access\..." (for `implements`) or "Extending
    //   deprecated class Drupal\block_content\Access\..." (for `extends`).
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\block_content\Access\AccessGroupAnd' => 'Drupal\Core\Access\AccessGroupAnd',
        'Drupal\block_content\Access\DependentAccessInterface' => 'Drupal\Core\Access\DependentAccessInterface',
        'Drupal\block_content\Access\RefinableDependentAccessInterface' => 'Drupal\Core\Access\RefinableDependentAccessInterface',
        'Drupal\block_content\Access\RefinableDependentAccessTrait' => 'Drupal\Core\Access\RefinableDependentAccessTrait',
    ]);
};
