#!/usr/bin/env python3
"""Search Drupal GitLab for contrib modules using deprecated APIs targeted by new rectors."""

import json
import time
import sys
import urllib.request
import urllib.parse
from collections import defaultdict

import os
TOKEN = os.environ["GITLAB_TOKEN"]
BASE = "https://git.drupalcode.org/api/v4"
GROUP_ID = 2
PATH_EXCLUSIONS = "-path:core -path:vendor -path:docroot -path:web -path:profiles -path:sites"
SLEEP_BETWEEN = 2  # seconds between search requests

SEARCHES = [
    # (rector_name, search_term)
    # Drupal 11
    ("ErrorCurrentErrorHandlerRector",               "Error::currentErrorHandler"),
    ("FileSystemBasenameToNativeRector",             "->basename("),
    ("LoadAllIncludesRector",                        "->loadAllIncludes("),
    ("MigrateSqlGetMigrationPluginManagerRector",    "->getMigrationPluginManager("),
    ("NodeStorageDeprecatedMethodsRector",           "->revisionIds("),
    ("NodeStorageDeprecatedMethodsRector",           "->userRevisionIds("),
    ("NodeStorageDeprecatedMethodsRector",           "->countDefaultLanguageRevisions("),
    ("PluginBaseIsConfigurableRector",               "->isConfigurable("),
    ("RemoveAutomatedCronSubmitHandlerRector",       "automated_cron_settings_submit"),
    ("RemoveCacheExpireOverrideRector",              "function cacheExpire("),
    ("RemoveHandlerBaseDefineExtraOptionsRector",    "function defineExtraOptions("),
    ("RemoveLinkWidgetValidateTitleElementRector",   "LinkWidget::validateTitleElement"),
    ("RemoveModuleHandlerAddModuleCallsRector",      "->addModule("),
    ("RemoveModuleHandlerDeprecatedMethodsRector",   "->writeCache("),
    ("RemoveModuleHandlerDeprecatedMethodsRector",   "->getHookInfo("),
    ("RemoveRootFromConvertDbUrlRector",             "convertDbUrlToConnectionInfo("),
    ("RemoveSetUriCallbackRector",                   "->setUriCallback("),
    ("RemoveStateCacheSettingRector",                "state_cache"),
    ("RemoveTrustDataCallRector",                    "->trustData("),
    ("RemoveTwigNodeTransTagArgumentRector",         "TwigNodeTrans"),
    ("RemoveUpdaterPostInstallMethodsRector",        "function postInstallTasks("),
    ("RemoveViewsRowCacheKeysRector",               "function getRowCacheKeys("),
    ("RenameStopProceduralHookScanRector",           "StopProceduralHookScan"),
    ("ReplaceAlphadecimalToIntNullRector",           "alphadecimalToInt("),
    ("ReplaceCommentManagerGetCountNewCommentsRector", "->getCountNewComments("),
    ("ReplaceCommentUriRector",                      "comment_uri("),
    ("ReplaceDateTimeRangeConstantsRector",          "DateTimeRangeConstantsInterface"),
    ("ReplaceEditorLoadRector",                      "editor_load("),
    ("ReplaceEntityOriginalPropertyRector",          "->original"),
    ("ReplaceEntityReferenceRecursiveLimitRector",   "RECURSIVE_RENDER_LIMIT"),
    ("ReplaceFieldgroupToFieldsetRector",            "'#type' => 'fieldgroup'"),
    ("ReplaceFileGetContentHeadersRector",           "file_get_content_headers("),
    ("ReplaceLocaleConfigBatchFunctionsRector",      "locale_config_batch_set_config_langcodes("),
    ("ReplaceLocaleConfigBatchFunctionsRector",      "locale_config_batch_refresh_name("),
    ("ReplaceNodeAccessViewAllNodesRector",          "node_access_view_all_nodes("),
    ("ReplaceNodeAddBodyFieldRector",                "node_add_body_field("),
    ("ReplaceNodeModuleProceduralFunctionsRector",   "node_type_get_names("),
    ("ReplaceNodeModuleProceduralFunctionsRector",   "node_get_type_label("),
    ("ReplaceNodeModuleProceduralFunctionsRector",   "node_mass_update("),
    ("ReplaceNodeSetPreviewModeRector",              "->setPreviewMode("),
    ("ReplacePdoFetchConstantsRector",               "PDO::FETCH_"),
    ("ReplaceRecipeRunnerInstallModuleRector",       "RecipeRunner::installModule("),
    ("ReplaceSessionManagerDeleteRector",            "SessionManager"),
    ("ReplaceSessionWritesWithRequestSessionRector", "$_SESSION["),
    ("ReplaceSystemPerformanceGzipKeyRector",        "css.gzip"),
    ("ReplaceThemeGetSettingRector",                 "theme_get_setting("),
    ("ReplaceThemeGetSettingRector",                 "_system_default_theme_features("),
    ("ReplaceUserSessionNamePropertyRector",         "UserSession"),
    ("ReplaceViewsProceduralFunctionsRector",        "views_get_view_result("),
    ("ReplaceViewsProceduralFunctionsRector",        "views_view_is_enabled("),
    ("ReplaceViewsProceduralFunctionsRector",        "views_enable_view("),
    ("StatementPrefetchIteratorFetchColumnRector",   "StatementPrefetchIterator"),
    ("StripMigrationDependenciesExpandArgRector",    "->getMigrationDependencies("),
    ("UseEntityTypeHasIntegerIdRector",              "->getEntityTypeIdKeyType("),
    ("UseEntityTypeHasIntegerIdRector",              "->entityTypeSupportsComments("),
    ("ViewsPluginHandlerManagerRector",              "Views::pluginManager("),
    ("ViewsPluginHandlerManagerRector",              "Views::handlerManager("),
    # Drupal 10
    ("ReplaceModuleHandlerGetNameRector",            "moduleHandler()->getName("),
    ("ReplaceRebuildThemeDataRector",                "->rebuildThemeData("),
    ("ReplaceRequestTimeConstantRector",             "REQUEST_TIME"),
    ("SystemTimeZonesRector",                        "system_time_zones("),
]


def api_get(path, params=None):
    url = f"{BASE}{path}"
    if params:
        url += "?" + urllib.parse.urlencode(params)
    req = urllib.request.Request(url, headers={"PRIVATE-TOKEN": TOKEN})
    with urllib.request.urlopen(req, timeout=15) as resp:
        return json.loads(resp.read())


def search_blobs(query):
    full_query = f"{PATH_EXCLUSIONS} {query}"
    try:
        results = api_get(f"/groups/{GROUP_ID}/search", {
            "scope": "blobs",
            "search": full_query,
            "per_page": 100,
        })
        return results
    except Exception as e:
        print(f"  ERROR: {e}", file=sys.stderr)
        return []


def get_project_name(project_id, cache={}):
    if project_id in cache:
        return cache[project_id]
    try:
        proj = api_get(f"/projects/{project_id}")
        name = proj.get("path_with_namespace", str(project_id))
        cache[project_id] = name
        time.sleep(0.5)
        return name
    except Exception:
        cache[project_id] = str(project_id)
        return str(project_id)


def main():
    # rector -> set of (project_id, filename) tuples
    rector_hits = defaultdict(set)
    # project_id -> set of rectors that hit it
    project_rectors = defaultdict(set)

    print(f"Running {len(SEARCHES)} searches...", file=sys.stderr)

    for i, (rector, term) in enumerate(SEARCHES):
        print(f"  [{i+1}/{len(SEARCHES)}] {rector}: {term!r}", file=sys.stderr)
        results = search_blobs(term)
        for r in results:
            pid = r["project_id"]
            fname = r.get("filename", "")
            rector_hits[rector].add((pid, fname))
            project_rectors[pid].add(rector)
        print(f"    → {len(results)} hits", file=sys.stderr)
        time.sleep(SLEEP_BETWEEN)

    # Collect all unique project IDs
    all_pids = set(project_rectors.keys())
    print(f"\nResolving {len(all_pids)} project names...", file=sys.stderr)
    pid_to_name = {}
    for pid in sorted(all_pids):
        pid_to_name[pid] = get_project_name(pid)
        print(f"  {pid} → {pid_to_name[pid]}", file=sys.stderr)

    # Output JSON for the markdown writer
    output = {
        "rector_hits": {
            rector: [
                {"project_id": pid, "project_name": pid_to_name.get(pid, str(pid)), "filename": fname}
                for pid, fname in sorted(hits)
            ]
            for rector, hits in rector_hits.items()
        },
        "project_rectors": {
            pid_to_name.get(pid, str(pid)): sorted(rectors)
            for pid, rectors in project_rectors.items()
        },
    }
    print(json.dumps(output, indent=2))


if __name__ == "__main__":
    main()
