#!/usr/bin/env python3
"""
Targeted search for D11 modules for rectors that had no results.
Uses more specific search terms to cut through noisy old projects.
"""

import json
import os
import re
import time
import sys
import urllib.request
import urllib.parse

TOKEN = os.environ["GITLAB_TOKEN"]
BASE = "https://git.drupalcode.org/api/v4"
GROUP_ID = 2
PATH_EXCLUSIONS = "-path:core -path:vendor -path:docroot -path:web -path:profiles -path:sites"
SLEEP = 0.5
MAX_PAGES = 10

# More specific terms that include namespace/class context to filter out old Drupal 6/7 code
TARGETED = [
    ("LoadAllIncludesRector",                        "loadAllIncludes ModuleHandler"),
    ("MigrateSqlGetMigrationPluginManagerRector",    "getMigrationPluginManager migrate"),
    ("PluginBaseIsConfigurableRector",               "isConfigurable PluginBase"),
    ("RemoveModuleHandlerAddModuleCallsRector",       "addModule ModuleHandlerInterface"),
    ("RemoveModuleHandlerDeprecatedMethodsRector",    "writeCache ModuleHandler"),
    ("RemoveModuleHandlerDeprecatedMethodsRector",    "getHookInfo ModuleHandler"),
    ("RemoveSetUriCallbackRector",                    "setUriCallback EntityType"),
    ("RemoveTrustDataCallRector",                     "trustData Config"),
    ("ReplaceCommentManagerGetCountNewCommentsRector","getCountNewComments CommentManager"),
    ("ReplaceEntityOriginalPropertyRector",           "->original EntityInterface"),
    ("ReplaceEntityOriginalPropertyRector",           "original ContentEntityBase"),
    ("ReplaceNodeSetPreviewModeRector",               "setPreviewMode NodeType"),
    ("ReplaceRebuildThemeDataRector",                 "rebuildThemeData ThemeHandler"),
    ("StripMigrationDependenciesExpandArgRector",     "getMigrationDependencies Migration"),
    ("UseEntityTypeHasIntegerIdRector",               "getEntityTypeIdKeyType"),
    ("UseEntityTypeHasIntegerIdRector",               "entityTypeSupportsComments"),
]

NOISE_PROJECTS = {"project/pt-br", "project/bg", "project/gladcamp", "project/binder",
                  "project/livediscussions", "project/mail_archive", "project/pushbutton_phptemplate",
                  "project/sitemenu", "project/contact_dir"}


def api_get(path, params=None):
    url = f"{BASE}{path}"
    if params:
        url += "?" + urllib.parse.urlencode(params)
    req = urllib.request.Request(url, headers={"PRIVATE-TOKEN": TOKEN})
    try:
        with urllib.request.urlopen(req, timeout=15) as resp:
            return json.loads(resp.read())
    except Exception as e:
        print(f"  API error {path}: {e}", file=sys.stderr)
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
        "search": "^11",
        "per_page": 10,
    })
    time.sleep(0.3)
    if not result:
        cache[project_id] = False
        return False
    for blob in result:
        if blob.get("filename", "").endswith(".info.yml"):
            cache[project_id] = True
            return True
    cache[project_id] = False
    return False


def main():
    rector_d11 = {}  # rector -> list of project names

    seen_pids = set()

    for rector, term in TARGETED:
        if rector not in rector_d11:
            rector_d11[rector] = []

        if rector_d11[rector]:  # already found something for this rector
            continue

        print(f"\n{rector}: {term!r}", file=sys.stderr)

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

            print(f"  page {page}: {len(result)} hits", file=sys.stderr)

            for blob in result:
                pid = blob["project_id"]
                if pid in seen_pids:
                    continue
                seen_pids.add(pid)

                pname = get_project_name(pid)
                if pname in NOISE_PROJECTS:
                    continue

                is_d11 = check_d11(pid)
                if is_d11:
                    print(f"  ✓ D11: {pname}", file=sys.stderr)
                    rector_d11[rector].append(pname)
                else:
                    print(f"    skip: {pname}", file=sys.stderr)

            if rector_d11[rector]:
                break  # found at least one, move to next rector

            if len(result) < 100:
                break

    print("\n=== Results ===", file=sys.stderr)
    for rector, projects in rector_d11.items():
        print(f"{rector}: {projects}", file=sys.stderr)

    print(json.dumps(rector_d11, indent=2))


if __name__ == "__main__":
    main()
