#!/usr/bin/env bash
# Sets up a Drupal 11 project with contrib modules that exercise all new rectors,
# wires in the local drupal-rector branch, and runs rector so you can review diffs.
#
# Usage: ./setup-rector-test.sh [target-directory]
#        Default target: ./drupal-rector-test

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
RECTOR_REPO="$(cd "$SCRIPT_DIR/.." && pwd)"
RECTOR_BRANCH="feature/digest-rectors"
FORK_URL="https://github.com/bbrala/drupal-rector"

TARGET_DIR="${1:-drupal-rector-test}"
TARGET_DIR="$(realpath "$TARGET_DIR")"

echo "==> Target directory: $TARGET_DIR"
echo "==> drupal-rector source: $RECTOR_REPO (branch: $RECTOR_BRANCH)"
echo ""

# ---------------------------------------------------------------------------
# 1. Scaffold the Drupal 11 project
# ---------------------------------------------------------------------------
if [ -d "$TARGET_DIR" ]; then
  echo "Directory $TARGET_DIR already exists — skipping composer create-project."
else
  echo "==> Creating Drupal 11 project…"
  composer create-project drupal/recommended-project "$TARGET_DIR" \
    --no-interaction \
    --no-install \
    --stability dev
fi

cd "$TARGET_DIR"

# ---------------------------------------------------------------------------
# 2. Wire in drupal-rector from the local clone (preferred) or the fork remote
# ---------------------------------------------------------------------------
echo ""
echo "==> Adding drupal-rector repository…"

# Use the local clone as a path repository so we get the exact working tree.
composer config repositories.drupal-rector \
  '{"type":"path","url":"'"$RECTOR_REPO"'","options":{"symlink":false}}'

# Also add the fork as a VCS fallback (lets composer resolve the branch name).
composer config repositories.drupal-rector-vcs \
  '{"type":"vcs","url":"'"$FORK_URL"'"}'

# Require drupal-rector dev branch.
composer require \
  "palantirnet/drupal-rector:dev-$RECTOR_BRANCH as 1.x-dev" \
  --no-update

# ---------------------------------------------------------------------------
# 3. Require contrib modules
#    Target: ≥2 modules per rector where possible.
#    See docs/contrib-modules-d11.md for the full coverage mapping.
#    7 rectors only have 1 module available (marked with *).
# ---------------------------------------------------------------------------
echo ""
echo "==> Requiring contrib modules…"

composer require --no-update \
  \
  `# ── 4-rector modules ──────────────────────────────────────────────────` \
  "drupal/drd:*"                              `# ReplaceEntityOriginalPropertyRector, ReplaceModuleHandlerGetNameRector, ReplaceSessionWritesWithRequestSessionRector, ReplaceSystemPerformanceGzipKeyRector` \
  "drupal/acquia_contenthub:*"               `# RemoveModuleHandlerAddModuleCallsRector, ReplaceEditorLoadRector, ReplacePdoFetchConstantsRector, ReplaceUserSessionNamePropertyRector` \
  "drupal/social:*"                          `# ReplaceCommentUriRector, ReplaceNodeModuleProceduralFunctionsRector, ReplaceViewsProceduralFunctionsRector, ViewsPluginHandlerManagerRector` \
  \
  `# ── 3-rector modules ──────────────────────────────────────────────────` \
  "drupal/searchstax:*"                      `# RemoveStateCacheSettingRector, RemoveTwigNodeTransTagArgumentRector, ViewsPluginHandlerManagerRector (2nd)` \
  "drupal/ai_agents:*"                       `# ReplaceEditorLoadRector (2nd), ReplaceNodeSetPreviewModeRector, ReplaceSystemPerformanceGzipKeyRector (2nd)` \
  \
  `# ── 2-rector modules ──────────────────────────────────────────────────` \
  "drupal/commerce_invoice:*"                `# ReplaceFileGetContentHeadersRector, UseEntityTypeHasIntegerIdRector` \
  "drupal/custom_field:*"                    `# ReplaceEntityReferenceRecursiveLimitRector, ReplaceViewsProceduralFunctionsRector (2nd)` \
  "drupal/entity_visibility_preview:*"       `# ReplaceSessionManagerDeleteRector, ReplaceSessionWritesWithRequestSessionRector (2nd)` \
  "drupal/views_dependent_filters:*"         `# RemoveHandlerBaseDefineExtraOptionsRector (*), RemoveTrustDataCallRector` \
  "drupal/group:*"                           `# RemoveTrustDataCallRector (2nd), RemoveUpdaterPostInstallMethodsRector` \
  "drupal/session_inspector:*"               `# ReplaceSessionManagerDeleteRector (2nd), ReplaceUserSessionNamePropertyRector (2nd)` \
  "drupal/sdx:*"                             `# RemoveModuleHandlerAddModuleCallsRector (2nd), RemoveStateCacheSettingRector (2nd)` \
  "drupal/search_api:*"                      `# PluginBaseIsConfigurableRector, RemoveTwigNodeTransTagArgumentRector (2nd)` \
  "drupal/schemadotorg:*"                    `# LoadAllIncludesRector, ReplaceRecipeRunnerInstallModuleRector (*)` \
  "drupal/smart_migrate_cli:*"               `# RemoveRootFromConvertDbUrlRector, StripMigrationDependenciesExpandArgRector` \
  "drupal/metatag:*"                         `# PluginBaseIsConfigurableRector (2nd), RemoveViewsRowCacheKeysRector` \
  "drupal/external_entity:*"                 `# ReplaceEntityOriginalPropertyRector (2nd), ReplaceEntityReferenceRecursiveLimitRector (2nd)` \
  "drupal/reassign_user_content:*"           `# ReplaceModuleHandlerGetNameRector (2nd), ReplaceNodeModuleProceduralFunctionsRector (2nd)` \
  \
  `# ── 1-rector modules (filling gaps) ─────────────────────────────────` \
  "drupal/openy:*"                           `# ReplaceRebuildThemeDataRector` \
  "drupal/bootstrap_italia:*"                `# ReplaceThemeGetSettingRector` \
  "drupal/view_usernames_node_author:*"      `# ReplaceNodeAccessViewAllNodesRector (*)` \
  "drupal/association:*"                     `# UseEntityTypeHasIntegerIdRector (2nd)` \
  "drupal/tome:*"                            `# ReplaceNodeAddBodyFieldRector` \
  "drupal/cmrf_form_processor:*"             `# RemoveCacheExpireOverrideRector` \
  "drupal/intl_date:*"                       `# SystemTimeZonesRector` \
  "drupal/geolocation:*"                     `# StripMigrationDependenciesExpandArgRector (2nd)` \
  "drupal/responsive_preview:*"              `# ReplaceNodeSetPreviewModeRector (2nd)` \
  "drupal/kart:*"                            `# ReplaceThemeGetSettingRector (2nd)` \
  "drupal/tmgmt:*"                           `# ReplaceFileGetContentHeadersRector (2nd)` \
  "drupal/config_track:*"                    `# LoadAllIncludesRector (2nd)` \
  "drupal/guswds:*"                          `# ReplaceRebuildThemeDataRector (2nd)` \
  "drupal/smart_date:*"                      `# SystemTimeZonesRector (2nd)` \
  "drupal/vcp4dates:*"                       `# RemoveCacheExpireOverrideRector (2nd)` \
  "drupal/gdpr:*"                            `# ReplacePdoFetchConstantsRector (2nd)` \
  "drupal/ai_eca:*"                          `# ReplaceNodeAddBodyFieldRector (2nd)` \
  "drupal/deprecation_status:*"              `# ReplaceDateTimeRangeConstantsRector (*)` \
  "drupal/gnode_request:*"                   `# RemoveUpdaterPostInstallMethodsRector (2nd)` \
  "drupal/google_analytics_counter:*"        `# ReplaceRequestTimeConstantRector` \
  "drupal/migmag:*"                          `# MigrateSqlGetMigrationPluginManagerRector` \
  "drupal/field_group_vertical_tabs:*"       `# ReplaceFieldgroupToFieldsetRector` \
  "drupal/ui_patterns_settings:*"            `# ReplaceFieldgroupToFieldsetRector (2nd)` \
  "drupal/tb_megamenu:*"                     `# NodeStorageDeprecatedMethodsRector (*)` \
  "drupal/automatic_updates:*"               `# ReplaceRequestTimeConstantRector (2nd)` \
  "drupal/sparql_entity_storage:*"           `# RemoveRootFromConvertDbUrlRector (2nd)` \
  "drupal/views_advanced_cache:*"            `# RemoveViewsRowCacheKeysRector (2nd)` \
  "drupal/captcha:*"                         `# RemoveModuleHandlerDeprecatedMethodsRector` \
  "drupal/ejectorseat:*"                     `# FileSystemBasenameToNativeRector (*)` \
  "drupal/smart_sql_idmap:*"                 `# MigrateSqlGetMigrationPluginManagerRector (2nd)` \
  "drupal/gadget:*"                          `# ReplaceCommentUriRector (2nd)` \
  "drupal/jsonld:*"                          `# RemoveModuleHandlerDeprecatedMethodsRector (2nd)` \
  "drupal/forum:*"                           `# ReplaceCommentManagerGetCountNewCommentsRector` \
  "drupal/history:*"                         `# ReplaceCommentManagerGetCountNewCommentsRector (2nd)` \
  "drupal/comment_mover:*"                   `# ReplaceAlphadecimalToIntNullRector` \
  "drupal/indieweb:*"                        `# ReplaceAlphadecimalToIntNullRector (2nd)` \
  "drupal/rabbit_hole:*"                     `# RemoveSetUriCallbackRector (*)`

# ---------------------------------------------------------------------------
# 4. Install everything
# ---------------------------------------------------------------------------
echo ""
echo "==> Running composer install (this may take a while)…"
composer install \
  --no-interaction \
  --ignore-platform-reqs \
  --prefer-dist

# ---------------------------------------------------------------------------
# 5. Initialise git and commit the baseline
# ---------------------------------------------------------------------------
echo ""
echo "==> Initialising git repo…"
if [ ! -d ".git" ]; then
  git init
fi

# Ignore vendor/ and web/core but keep contrib module source for rector + diffing.
cat > .gitignore << 'GITIGNORE'
/vendor/
/web/core/
/web/sites/default/settings.php
/web/sites/default/services.yml
*.orig
GITIGNORE

git add .
git commit -m "Install Drupal 11 + contrib modules for rector testing" --allow-empty

# ---------------------------------------------------------------------------
# 6. Write rector.php
# ---------------------------------------------------------------------------
echo ""
echo "==> Writing rector.php…"
cat > rector.php << 'RECTOR'
<?php

declare(strict_types=1);

use DrupalRector\Set\Drupal10SetList;
use DrupalRector\Set\Drupal11SetList;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/web/modules/contrib',
    ])
    ->withSets([
        Drupal10SetList::DRUPAL_10,
        Drupal11SetList::DRUPAL_11,
    ]);
RECTOR

git add rector.php
git commit -m "Add rector.php config targeting web/modules/contrib"

# ---------------------------------------------------------------------------
# 7. Run rector --dry-run to preview changes
# ---------------------------------------------------------------------------
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " Running rector --dry-run (no files modified)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
vendor/bin/rector process --dry-run 2>&1 || true

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " Next steps"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "  Apply changes:"
echo "    cd $TARGET_DIR && vendor/bin/rector process"
echo ""
echo "  Review diff:"
echo "    git diff"
echo ""
echo "  Rector coverage notes:"
echo "  * FileSystemBasenameToNativeRector  — only 1 module (ejectorseat)"
echo "  * NodeStorageDeprecatedMethodsRector — only 1 module (tb_megamenu)"
echo "  * RemoveHandlerBaseDefineExtraOptionsRector — only 1 module (views_dependent_filters)"
echo "  * RemoveSetUriCallbackRector — only 1 module (rabbit_hole)"
echo "  * ReplaceDateTimeRangeConstantsRector — only 1 module (deprecation_status)"
echo "  * ReplaceNodeAccessViewAllNodesRector — only 1 module (view_usernames_node_author)"
echo "  * ReplaceRecipeRunnerInstallModuleRector — only 1 module (schemadotorg)"
echo "  * 6 rectors have no D11 contrib usage found (see docs/contrib-modules-d11.md)"
