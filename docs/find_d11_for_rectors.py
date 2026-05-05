#!/usr/bin/env python3
"""
For rectors with no D11-compatible projects yet, paginate through more search
results to find D11-compatible modules. Merges into existing results.
"""

import json
import os
import re
import time
import sys
import urllib.request
import urllib.parse
from collections import defaultdict

TOKEN = os.environ["GITLAB_TOKEN"]
BASE = "https://git.drupalcode.org/api/v4"
GROUP_ID = 2
PATH_EXCLUSIONS = "-path:core -path:vendor -path:docroot -path:web -path:profiles -path:sites"
SLEEP = 0.5
MAX_PAGES = 10  # up to 1000 results per search term

SEARCHES = [
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
    try:
        with urllib.request.urlopen(req, timeout=15) as resp:
            return json.loads(resp.read())
    except Exception as e:
        print(f"  API error: {e}", file=sys.stderr)
        return None


def get_project_name(project_id, cache={}):
    if project_id in cache:
        return cache[project_id]
    result = api_get(f"/projects/{project_id}")
    name = result.get("path_with_namespace", str(project_id)) if result else str(project_id)
    cache[project_id] = name
    time.sleep(0.3)
    return name


def check_d11(project_id, cache={}):
    if project_id in cache:
        return cache[project_id]
    result = api_get(f"/projects/{project_id}/search", {
        "scope": "blobs",
        "search": "core_version_requirement",
        "per_page": 5,
    })
    if not result:
        cache[project_id] = False
        return False
    for blob in result:
        if not blob.get("filename", "").endswith(".info.yml"):
            continue
        if re.search(r'core_version_requirement:[^\n]*\b11\b', blob.get("data", "")):
            cache[project_id] = True
            return True
    cache[project_id] = False
    return False


def main():
    # Load existing D11 results
    with open("docs/contrib-modules-d11.md") as f:
        existing_md = f.read()

    # Parse which rectors already have D11 projects
    rectors_with_d11 = set()
    current_rector = None
    for line in existing_md.splitlines():
        if line.startswith("### "):
            current_rector = line[4:].strip()
        elif line.startswith("- [") and current_rector:
            rectors_with_d11.add(current_rector)

    print(f"Rectors already with D11 projects: {len(rectors_with_d11)}", file=sys.stderr)

    # Load existing search results to know which project IDs we already checked
    with open("docs/search_results.json") as f:
        existing = json.load(f)

    already_checked_pids = set()
    for hits in existing["rector_hits"].values():
        for h in hits:
            already_checked_pids.add(h["project_id"])

    # Group searches by rector, only process rectors without D11 projects yet
    rector_searches = defaultdict(list)
    for rector, term in SEARCHES:
        rector_searches[rector].append(term)

    rectors_to_search = [r for r in rector_searches if r not in rectors_with_d11]
    print(f"Rectors needing D11 projects: {rectors_to_search}", file=sys.stderr)

    # For each rector without D11 projects, paginate through results
    rector_d11_found = defaultdict(list)  # rector -> list of project names

    for rector in rectors_to_search:
        print(f"\nSearching for D11 modules for {rector}...", file=sys.stderr)
        for term in rector_searches[rector]:
            found = False
            for page in range(1, MAX_PAGES + 1):
                full_query = f"{PATH_EXCLUSIONS} {term}"
                result = api_get(f"/groups/{GROUP_ID}/search", {
                    "scope": "blobs",
                    "search": full_query,
                    "per_page": 100,
                    "page": page,
                })
                time.sleep(SLEEP)
                if not result:
                    break
                print(f"  {term!r} page {page}: {len(result)} hits", file=sys.stderr)
                for blob in result:
                    pid = blob["project_id"]
                    if pid in already_checked_pids:
                        continue
                    already_checked_pids.add(pid)
                    pname = get_project_name(pid)
                    is_d11 = check_d11(pid)
                    time.sleep(SLEEP)
                    if is_d11:
                        print(f"    ✓ D11: {pname}", file=sys.stderr)
                        rector_d11_found[rector].append(pname)
                        found = True
                if len(result) < 100:
                    break  # no more pages
            if found:
                break  # found D11 modules for this rector via this term, try next rector

    # Output summary
    print("\n=== Summary ===", file=sys.stderr)
    for rector, projects in rector_d11_found.items():
        print(f"{rector}: {projects}", file=sys.stderr)

    print(json.dumps(dict(rector_d11_found), indent=2))


if __name__ == "__main__":
    main()
