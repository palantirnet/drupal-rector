#!/usr/bin/env bash
# Sets up a Drupal 11 DDEV project with contrib modules that exercise all new
# rectors, wires in the local drupal-rector branch, and runs rector so you can
# review the resulting diff.
#
# Usage: ./scripts/setup-rector-test.sh [project-name]
#   Default project name: drupal-rector-test
#   Default location:     ../../<project-name>  (sibling of the rector repo)

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
RECTOR_REPO="$(cd "$SCRIPT_DIR/.." && pwd)"
RECTOR_BRANCH="feature/digest-rectors"

PROJECT_NAME="${1:-drupal-rector-test}"
# Two levels up from this script (scripts/ → repo-root → parent) then the project name.
TARGET_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)/$PROJECT_NAME"

echo "==> Project directory : $TARGET_DIR"
echo "==> drupal-rector repo : $RECTOR_REPO ($RECTOR_BRANCH)"
echo ""

# ---------------------------------------------------------------------------
# 1. Create project directory and configure DDEV
# ---------------------------------------------------------------------------
mkdir -p "$TARGET_DIR"
cd "$TARGET_DIR"

echo "==> Configuring DDEV…"
ddev config \
  --project-type=drupal11 \
  --docroot=web \
  --project-name="$PROJECT_NAME"

# Mount the local rector clone inside the DDEV container so the PATH
# composer repository works without pushing to GitHub first.
cat > .ddev/docker-compose.mounts.yaml << DOCKERCOMPOSE
services:
  web:
    volumes:
    - "$RECTOR_REPO:/mnt/drupal-rector"
DOCKERCOMPOSE

ddev start -y

# ---------------------------------------------------------------------------
# 2. Scaffold Drupal 11
# ---------------------------------------------------------------------------
echo ""
echo "==> Scaffolding Drupal 11 via composer create-project…"
ddev composer create-project "drupal/recommended-project:^11" . \
  --no-interaction \
  --stability dev

# Pre-approve all composer plugins upfront so no interactive prompts appear
# during any subsequent require/update calls.
ddev composer config allow-plugins.tbachert/spi true

ddev composer require drush/drush --no-interaction

echo ""
echo "==> Installing Drupal site…"
ddev drush site:install --account-name=admin --account-pass=admin -y

# ---------------------------------------------------------------------------
# 3. Wire in drupal-rector from the local clone
# ---------------------------------------------------------------------------
echo ""
echo "==> Wiring in drupal-rector (local clone via DDEV mount)…"

# PATH repository — resolves to the mounted rector clone inside the container.
# Listed as the only source; no VCS fallback needed (and SSH keys aren't
# available inside DDEV containers anyway).
ddev composer config repositories.drupal-rector \
  '{"type":"path","url":"/mnt/drupal-rector","options":{"symlink":true}}'

ddev composer require \
  "palantirnet/drupal-rector:dev-$RECTOR_BRANCH as 1.x-dev" \
  --no-interaction

# ---------------------------------------------------------------------------
# 4. Require contrib modules (≥2 per rector where possible)
#    See docs/contrib-modules-d11.md for the full coverage mapping.
#    Note: 8 rectors have no testable D11 contrib module (see comments in test script).
# ---------------------------------------------------------------------------
echo ""
echo "==> Requiring contrib modules…"

# Batch 1 — multi-rector modules (high-value)
ddev composer require --no-update \
  "drupal/drd:*" \
  "drupal/acquia_contenthub:*" \
  "drupal/searchstax:*" \
  "drupal/ai_agents:*" \
  "drupal/ckeditor5_plugin_pack:*" \
  "drupal/bootstrap5:*" \
  "drupal/commerce_invoice:*" \
  "drupal/custom_field:*" \
  "drupal/entity_visibility_preview:*" \
  "drupal/views_dependent_filters:*" \
  "drupal/group:*" \
  "drupal/session_inspector:*" \
  "drupal/sdx:*" \
  "drupal/search_api:*" \
  "drupal/schemadotorg:*" \
  "drupal/smart_migrate_cli:*" \
  "drupal/metatag:*" \
  "drupal/external_entity:*" \
  "drupal/reassign_user_content:*"

# Batch 2 — single-rector gap-fillers
ddev composer require --no-update \
  "drupal/tara:*" \
  "drupal/vani:*" \
  "drupal/view_usernames_node_author:*" \
  "drupal/association:*" \
  "drupal/tome:*" \
  "drupal/cmrf_form_processor:*" \
  "drupal/intl_date:*" \
  "drupal/responsive_preview:*" \
  "drupal/tmgmt:*" \
  "drupal/config_track:*" \
  "drupal/site_guardian:*" \
  "drupal/smart_date:*" \
  "drupal/vcp4dates:*" \
  "drupal/gdpr:*" \
  "drupal/ai_eca:*" \
  "drupal/deprecation_status:*" \
  "drupal/gnode_request:*" \
  "drupal/google_analytics_counter:*" \
  "drupal/migmag:*" \
  "drupal/field_group_vertical_tabs:*" \
  "drupal/ui_patterns_settings:*" \
  "drupal/tb_megamenu:*" \
  "drupal/automatic_updates:*" \
  "drupal/sparql_entity_storage:*" \
  "drupal/views_advanced_cache:*" \
  "drupal/captcha:*" \
  "drupal/ejectorseat:*" \
  "drupal/smart_sql_idmap:*" \
  "drupal/jsonld:*" \
  "drupal/forum:*" \
  "drupal/history:*" \
  "drupal/comment_mover:*" \
  "drupal/rabbit_hole_href:*" \
  "drupal/addanother:*" \
  "drupal/quicktabs:*" \
  "drupal/entity_usage:*" \
  "drupal/media_auto_publication:*" \
  "drupal/migrate_tools:*"

echo ""
echo "==> Running composer update to resolve all requirements…"
ddev composer update --no-interaction --with-all-dependencies

# ---------------------------------------------------------------------------
# 5. Initialise git and commit the installed baseline
#    (vendor/ and web/core/ excluded; web/modules/contrib/ tracked so
#    rector changes show up in git diff)
# ---------------------------------------------------------------------------
echo ""
echo "==> Initialising git repository…"
if [ ! -d ".git" ]; then
  git init
fi

cat > .gitignore << 'GITIGNORE'
/vendor/
/web/core/
/web/sites/default/settings.php
/web/sites/default/services.yml
/web/sites/default/files/
*.orig
GITIGNORE

git add .
git commit -m "Install Drupal 11 + contrib modules for rector testing"

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
    ->withFileExtensions(['php', 'module', 'theme', 'install', 'profile', 'inc', 'engine'])
    ->withSets([
        Drupal10SetList::DRUPAL_10,
        Drupal11SetList::DRUPAL_11,
    ]);
RECTOR

git add rector.php

# ---------------------------------------------------------------------------
# 7. Generate per-rector test script (run this inside ddev ssh)
# ---------------------------------------------------------------------------
mkdir -p scripts
cat > scripts/test-rectors.sh << 'TESTSCRIPT'
#!/usr/bin/env bash
# Per-rector test runner. Run this INSIDE the DDEV container:
#   ddev ssh
#   bash /var/www/html/scripts/test-rectors.sh [RectorName]
#
# With no argument: runs all rectors in sequence.
# With a rector class name: runs only that one rector.
# Output is tee'd to /var/www/html/rector-test.log

set -euo pipefail
cd /var/www/html

LOG=/var/www/html/rector-test.log
CONTRIB=web/modules/contrib
FILTER="${1:-}"

run_test() {
    local rector="$1"
    shift
    local mods=("$@")

    # Skip if a filter is set and doesn't match
    if [ -n "$FILTER" ] && [ "$FILTER" != "$rector" ]; then
        return
    fi

    # Derive FQCN by checking which namespace the rector lives in
    local fqcn
    if [ -f "vendor/palantirnet/drupal-rector/src/Drupal11/Rector/Deprecation/${rector}.php" ]; then
        fqcn="DrupalRector\\Drupal11\\Rector\\Deprecation\\${rector}"
    else
        fqcn="DrupalRector\\Drupal10\\Rector\\Deprecation\\${rector}"
    fi

    # Resolve installed module directories
    local paths=()
    for mod in "${mods[@]}"; do
        [ -d "$CONTRIB/$mod" ] && paths+=("$CONTRIB/$mod")
    done

    echo "" | tee -a "$LOG"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" | tee -a "$LOG"
    printf " %s\n" "$rector" | tee -a "$LOG"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" | tee -a "$LOG"

    if [ ${#paths[@]} -eq 0 ]; then
        echo "  SKIP — no modules installed for this rector" | tee -a "$LOG"
        return
    fi

    printf "  Rector: %s\n" "$fqcn" | tee -a "$LOG"
    printf "  Modules: %s\n" "${paths[*]}" | tee -a "$LOG"
    echo "" | tee -a "$LOG"

    timeout 30 vendor/bin/rector process "${paths[@]}" --only="$fqcn" --dry-run --no-cache 2>&1 | tee -a "$LOG" || true

    echo "" | tee -a "$LOG"
    git diff "${paths[@]}" | tee -a "$LOG" || true

    # Reset files so the next rector starts clean
    git checkout -- "${paths[@]}" 2>/dev/null || true
}

echo "Rector test run — $(date)" | tee "$LOG"
echo "Log: $LOG" | tee -a "$LOG"

# ── Drupal 11 rectors ──────────────────────────────────────────────────────
run_test ErrorCurrentErrorHandlerRector
    # No contrib usage: Error::currentErrorHandler() deprecated D11.3.0; devel 5.x already cleaned up, no other hits

run_test FileSystemBasenameToNativeRector \
    ejectorseat

run_test LoadAllIncludesRector \
    config_track schemadotorg

run_test MigrateSqlGetMigrationPluginManagerRector \
    feeds_migrate migmag smart_sql_idmap

run_test NodeStorageDeprecatedMethodsRector \
    tb_megamenu

run_test PluginBaseIsConfigurableRector \
    metatag search_api

run_test RemoveAutomatedCronSubmitHandlerRector
    # No contrib usage: automated_cron_settings_submit deprecated D11.4.0, no contrib calls found

run_test RemoveCacheExpireOverrideRector \
    cmrf_form_processor vcp4dates

run_test RemoveHandlerBaseDefineExtraOptionsRector \
    views_dependent_filters

run_test RemoveLinkWidgetValidateTitleElementRector
    # No contrib usage: LinkWidget::validateTitleElement() deprecated D11.4.0, no contrib calls found

run_test RemoveModuleHandlerAddModuleCallsRector \
    acquia_contenthub sdx

run_test RemoveModuleHandlerDeprecatedMethodsRector \
    captcha jsonld

run_test RemoveRootFromConvertDbUrlRector \
    smart_migrate_cli sparql_entity_storage

run_test RemoveSetUriCallbackRector \
    rabbit_hole_href

run_test RemoveStateCacheSettingRector \
    searchstax sdx

run_test RemoveTrustDataCallRector \
    views_dependent_filters group

run_test RemoveTwigNodeTransTagArgumentRector \
    searchstax

run_test RemoveUpdaterPostInstallMethodsRector \
    group gnode_request

run_test RemoveViewsRowCacheKeysRector \
    metatag views_advanced_cache

run_test RenameStopProceduralHookScanRector
    # No contrib usage: StopProceduralHookScan attribute deprecated D11.2.0, niche — no contrib usage found

run_test ReplaceAlphadecimalToIntNullRector \
    comment_mover indieweb

run_test ReplaceCommentManagerGetCountNewCommentsRector \
    forum history

run_test ReplaceCommentUriRector
    # No contrib usage: comment_uri() deprecated D11.3.0; Social 13.x already cleaned up (issue #3432522), no other D11 hits

run_test ReplaceDateTimeRangeConstantsRector \
    deprecation_status

run_test ReplaceEditorLoadRector \
    acquia_contenthub ckeditor5_plugin_pack

run_test ReplaceEntityOriginalPropertyRector \
    entity_usage media_auto_publication

run_test ReplaceEntityReferenceRecursiveLimitRector
    # No contrib usage: RECURSIVE_RENDER_LIMIT removed from core before D11;
    # all D11-compatible modules either define their own constant or hardcode 20.
    # custom_field/external_entity define the constant, they don't reference core's.

run_test ReplaceFieldgroupToFieldsetRector \
    field_group_vertical_tabs ui_patterns_settings

run_test ReplaceFileGetContentHeadersRector \
    commerce_invoice tmgmt

run_test ReplaceLocaleConfigBatchFunctionsRector
    # No contrib usage: locale_config_batch_* deprecated D11.1.0; all GitLab hits were bundled core copies, not contrib code

run_test ReplaceModuleHandlerGetNameRector \
    drd reassign_user_content

run_test ReplaceNodeAccessViewAllNodesRector \
    view_usernames_node_author

run_test ReplaceNodeAddBodyFieldRector \
    tome ai_eca

run_test ReplaceNodeModuleProceduralFunctionsRector \
    reassign_user_content addanother

run_test ReplaceNodeSetPreviewModeRector \
    ai_agents responsive_preview

run_test ReplacePdoFetchConstantsRector \
    acquia_contenthub gdpr

run_test ReplaceRecipeRunnerInstallModuleRector \
    schemadotorg

run_test ReplaceSessionManagerDeleteRector \
    entity_visibility_preview session_inspector

run_test ReplaceSessionWritesWithRequestSessionRector \
    drd entity_visibility_preview

run_test ReplaceSystemPerformanceGzipKeyRector \
    drd bootstrap5

run_test ReplaceThemeGetSettingRector \
    tara vani

run_test ReplaceUserSessionNamePropertyRector \
    acquia_contenthub session_inspector

run_test ReplaceViewsProceduralFunctionsRector \
    custom_field quicktabs

run_test StatementPrefetchIteratorFetchColumnRector
    # No contrib usage: fetchColumn() removed at D10.0; all D11-compatible modules already resolved this

run_test StripMigrationDependenciesExpandArgRector \
    migrate_tools

run_test UseEntityTypeHasIntegerIdRector \
    commerce_invoice association

run_test ViewsPluginHandlerManagerRector \
    searchstax search_api metatag

# ── Drupal 10 rectors ──────────────────────────────────────────────────────
run_test ReplaceModuleHandlerGetNameRector \
    drd reassign_user_content

run_test ReplaceRebuildThemeDataRector \
    site_guardian

run_test ReplaceRequestTimeConstantRector \
    google_analytics_counter automatic_updates

run_test SystemTimeZonesRector \
    intl_date smart_date

echo "" | tee -a "$LOG"
echo "Done. Full log: $LOG" | tee -a "$LOG"
TESTSCRIPT

chmod +x scripts/test-rectors.sh
git add rector.php scripts/test-rectors.sh
git commit -m "Add rector.php and per-rector test script"

# ---------------------------------------------------------------------------
# 8. Print manual run instructions
# ---------------------------------------------------------------------------
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo " Setup complete."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "  cd $TARGET_DIR && ddev ssh"
echo ""
echo "  # Run all rectors, each against its own modules only:"
echo "  bash /var/www/html/scripts/test-rectors.sh"
echo ""
echo "  # Run a single rector:"
echo "  bash /var/www/html/scripts/test-rectors.sh LoadAllIncludesRector"
echo ""
echo "  # Log is written to: $TARGET_DIR/rector-test.log"
echo ""
echo "  Site: run 'ddev launch'  |  Admin: admin / admin"
echo ""
echo "  Coverage notes (see docs/contrib-modules-d11.md):"
echo "  * 8 rectors skipped — pattern exhausted or no D11 contrib usage found"
echo "    (ErrorCurrentErrorHandler, RemoveAutomatedCronSubmitHandler,"
echo "     RemoveLinkWidgetValidateTitleElement, RenameStopProceduralHookScan,"
echo "     ReplaceCommentUri, ReplaceEntityReferenceRecursiveLimit,"
echo "     ReplaceLocaleConfigBatchFunctions, StatementPrefetchIteratorFetchColumn)"
