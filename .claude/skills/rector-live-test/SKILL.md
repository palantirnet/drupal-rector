---
name: rector-live-test
description: Finds D11-compatible contrib modules that exercise a rector and runs it against them. Uses search.tresbien.tech as primary search tool, falls back to Drupal GitLab API. Pass rector class name or issue number as argument.
argument-hint: "<RectorClassName or issue-number>"
allowed-tools: Read, Bash, Glob
---

# Rector Live Test

Find real contrib modules that use the deprecated API a rector targets, then run the rector against them to verify it transforms real-world code correctly.

## Input

`$ARGUMENTS` — either:
- Rector class name: `ReplaceSessionManagerDeleteRector`
- Issue number: `3577376`

## Steps

### 1. Resolve the rector

If given a class name, find the source file:
```bash
find src -name "<ClassName>.php"
```

If given an issue number, check `docs/rector-index.yml` (regenerate if needed) for the class name, then find the source file.

Read the rector source to extract:
- The deprecated method/function/constant name (look in `isName()` calls, constants, or `FUNCTION_MAP`)
- The deprecated class/interface name (from `isObjectType()` guards)

### 2. Search for contrib modules

**Primary: `search.tresbien.tech`**

Navigate to the search tool and search for the deprecated API pattern. This site searches Drupal contrib module code indexed from drupal.org.

Construct the search query:
- For method calls: `->methodName(`
- For function calls: `functionName(`
- For class constants: `ClassName::CONSTANT_NAME`
- For properties: `->propertyName`

If fewer than 3 results, also check `docs/contrib-module-search.md` for pre-discovered matches from session 18.

**Fallback: Drupal GitLab API blob search**

If `search.tresbien.tech` yields no results or is unavailable:

```bash
QUERY="<urlencoded_search_term>"
curl -s "https://git.drupalcode.org/search?group_id=2&scope=blobs&search=-path%3Acore+-path%3Avendor+-path%3Adocroot+-path%3Aweb+-path%3Aprofiles+-path%3Asites+$QUERY" \
  | grep -o 'data-project="[^"]*"' | sort -u | head -20
```

### 3. Filter to D11-compatible modules

For each module found, check its `.info.yml` for `core_version_requirement`:
```bash
# Example using GitLab API for a specific module
curl -s "https://git.drupalcode.org/api/v4/projects/<group>%2F<module>/repository/files/<module>.info.yml/raw?ref=HEAD"
```

Keep only modules where `core_version_requirement` includes Drupal 11 (e.g., `^8 || ^9 || ^10 || ^11` or `>=10`).

If no D11-compatible modules are found, report:
```
No D11-compatible contrib modules found for <RectorName>.
Try manually: https://git.drupalcode.org/search?group_id=2&scope=blobs&search=<query>
Update docs/contrib-module-search.md with findings.
```

### 4. Run the rector (if setup script exists)

Check whether the integration test setup exists:
```bash
ls scripts/setup-rector-test.sh scripts/drupal-rector-test/ 2>/dev/null
```

**If the setup exists:**

Add target modules to `scripts/drupal-rector-test/composer.json`, then run:
```bash
bash scripts/setup-rector-test.sh --rector <ClassName> --no-cache
```

Always pass `--no-cache` to prevent stale rector results from a prior run.

Parse the output for:
- `X file(s) changed` — success, the rector transformed code
- `0 files changed` or no output — investigate (see below)

**If the setup does not exist:**

Report that integration testing requires `scripts/setup-rector-test.sh` and provide manual instructions:

1. Clone a D11-compatible module into a Drupal 11 site
2. Run: `ddev exec vendor/bin/rector process <module-path> --config drupal-rector.php --no-cache`
3. Check the diff for expected transformations

### 5. Report results

For each tested module, report:
```
<ModuleName> — <files_changed> file(s) changed
  Transformations: <summary of what changed>
```

### 6. Diagnose zero-match results

If a module was found but 0 files were changed, investigate using `docs/no-match-investigation.md` (if it exists). Common causes:

| Cause | Diagnosis | Fix |
|-------|-----------|-----|
| Untyped code | The variable calling the deprecated method has no type annotation | The rector is working correctly — untyped code is intentionally skipped to avoid false positives |
| Wrong module version | The module has already updated its code | Try an older tagged version |
| Rector class mismatch | Wrong rector class was run | Verify the rector targets the exact deprecated API used in the module |
| Rector cache | Old cache silently skips files | Already handled by `--no-cache` |
| getNodeTypes mismatch | The node type returned by the rector doesn't match the actual AST node | Read the digest rule and the actual module code to compare |

### 7. Update contrib search doc

After running, update `docs/contrib-module-search.md` with the modules found (or confirmed absent):
```
### <RectorClassName>
- **Search:** `<search_term>`
- **Modules found:** <module1>, <module2> or —
```
