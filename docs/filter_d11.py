#!/usr/bin/env python3
"""Filter search results to only projects with Drupal 11 support, then write markdown."""

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
SLEEP = 0.4

def api_get(path, params=None):
    url = f"{BASE}{path}"
    if params:
        url += "?" + urllib.parse.urlencode(params)
    req = urllib.request.Request(url, headers={"PRIVATE-TOKEN": TOKEN})
    try:
        with urllib.request.urlopen(req, timeout=10) as resp:
            return json.loads(resp.read())
    except Exception as e:
        return None


def supports_d11(project_id, project_name, cache={}):
    if project_id in cache:
        return cache[project_id]

    # Search for .info.yml files in the project containing core_version_requirement
    result = api_get(f"/projects/{project_id}/search", {
        "scope": "blobs",
        "search": "core_version_requirement",
        "per_page": 5,
    })
    if not result:
        cache[project_id] = False
        return False

    for blob in result:
        fname = blob.get("filename", "")
        if not fname.endswith(".info.yml"):
            continue
        data = blob.get("data", "")
        # Match: core_version_requirement: ^10 || ^11, ^11, >=11, ~11, etc.
        if re.search(r'core_version_requirement:[^\n]*\b11\b', data):
            cache[project_id] = True
            return True

    cache[project_id] = False
    return False


def main():
    with open("docs/search_results.json") as f:
        data = json.load(f)

    rector_hits = data["rector_hits"]

    # Collect unique project IDs across all rectors
    # Map project_name -> project_id (we stored both in results)
    name_to_id = {}
    for rector, hits in rector_hits.items():
        for h in hits:
            name_to_id[h["project_name"]] = h["project_id"]

    all_projects = list(name_to_id.items())
    print(f"Checking {len(all_projects)} projects for D11 support...", file=sys.stderr)

    d11_projects = set()
    for i, (name, pid) in enumerate(all_projects):
        ok = supports_d11(pid, name)
        if ok:
            d11_projects.add(name)
            print(f"  [{i+1}/{len(all_projects)}] ✓ {name}", file=sys.stderr)
        else:
            print(f"  [{i+1}/{len(all_projects)}]   {name}", file=sys.stderr)
        time.sleep(SLEEP)

    print(f"\n{len(d11_projects)} projects support D11.", file=sys.stderr)

    # Build filtered rector -> [project_name] mapping
    rector_d11_projects = defaultdict(list)
    project_rectors = defaultdict(list)

    for rector, hits in rector_hits.items():
        seen = set()
        for h in hits:
            pname = h["project_name"]
            if pname in d11_projects and pname not in seen:
                rector_d11_projects[rector].append(pname)
                project_rectors[pname].append(rector)
                seen.add(pname)

    # Write markdown
    lines = [
        "# Contrib Modules Using Deprecated APIs (D11 Compatible)",
        "",
        "Modules found via Drupal GitLab code search, filtered to those with `core_version_requirement` supporting Drupal 11.",
        "",
        "---",
        "",
        "## By Rector",
        "",
    ]

    for rector in sorted(rector_d11_projects.keys()):
        projects = sorted(rector_d11_projects[rector])
        lines.append(f"### {rector}")
        if projects:
            for p in projects:
                short = p.replace("project/", "")
                lines.append(f"- [{short}](https://www.drupal.org/project/{short})")
        else:
            lines.append("- *(no D11 contrib modules found)*")
        lines.append("")

    # Rectors with no hits at all
    all_rectors = set()
    for rector, hits in rector_hits.items():
        all_rectors.add(rector)
    for rector in sorted(all_rectors - set(rector_d11_projects.keys())):
        lines.append(f"### {rector}")
        lines.append("- *(no hits)*")
        lines.append("")

    lines += [
        "---",
        "",
        "## By Module (modules covering multiple rectors)",
        "",
    ]

    multi = {p: rs for p, rs in project_rectors.items() if len(rs) > 1}
    for pname in sorted(multi.keys(), key=lambda p: -len(multi[p])):
        short = pname.replace("project/", "")
        rectors = multi[pname]
        lines.append(f"### [{short}](https://www.drupal.org/project/{short}) — {len(rectors)} rectors")
        for r in sorted(rectors):
            lines.append(f"- {r}")
        lines.append("")

    md = "\n".join(lines)
    out = "docs/contrib-modules-d11.md"
    with open(out, "w") as f:
        f.write(md)
    print(f"\nWrote {out}", file=sys.stderr)
    print(md)


if __name__ == "__main__":
    main()
