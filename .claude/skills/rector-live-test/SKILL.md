---
name: rector-live-test
description: Finds D11-compatible contrib modules that exercise a rector and runs it against them. Uses search.tresbien.tech as primary search tool, falls back to Drupal GitLab API. Pass rector class name or issue number as argument.
argument-hint: "<RectorClassName or issue-number>"
allowed-tools: Read, Bash, Glob, WebFetch, WebSearch
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

Use `WebFetch` to search this Drupal contrib code index. The base URL is:

```
https://search.tresbien.tech/search?q=<urlencoded_query>&num=0&ctx=0
```

**Always include `-r:drupal`** to exclude Drupal core from results (use `-r:drupal`, NOT `-r:core`).

Standard query construction:
- Method call: `-r:drupal ->methodName(`
- Function call: `-r:drupal functionName(`
- Class constant: `-r:drupal ClassName::CONSTANT_NAME`
- Property access: `-r:drupal ->propertyName`

Additional filters to add as needed:
- `f:\.php$` — PHP files only (add `f:\.module$` if the pattern may appear in `.module` files)
- `-f:test` — exclude test files when you want production code only
- `lang:php` — PHP language filter
- `case:yes` — force case-sensitive match

Example for `->delete(` on SessionManager:
```
https://search.tresbien.tech/search?q=-r%3Adrupal+-%3Edelete(&num=0&ctx=0
```

Parse the fetched page for matching file paths and extract the module/project name from the path prefix.

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
```

### 4. Run the rector

**Check if the DDEV test project exists:**
```bash
ls ~/projects/drupal-rector-test/.ddev 2>/dev/null && echo "exists" || echo "not found"
```

**If not found**, run the one-time setup (takes ~10 minutes):
```bash
bash .claude/skills/rector-live-test/setup-rector-test.sh
```
This creates a Drupal 11 site at `~/projects/drupal-rector-test` with a broad set of
contrib modules pre-installed. Running the script again is safe — it detects the existing
project and just ensures DDEV is started.

**If a module found in step 2 is not pre-installed**, add it before running:
```bash
cd ~/projects/drupal-rector-test
ddev composer require drupal/<module> --no-interaction
```

**Resolve the FQCN** based on where the rector source lives:
- `src/Drupal11/…` → `DrupalRector\Drupal11\Rector\Deprecation\<ClassName>`
- `src/Drupal10/…` → `DrupalRector\Drupal10\Rector\Deprecation\<ClassName>`
- `src/Rector/…`   → `DrupalRector\Rector\Deprecation\<ClassName>`

**Run rector** against the found modules:
```bash
cd ~/projects/drupal-rector-test
ddev exec -d /var/www/html \
  vendor/bin/rector process \
  web/modules/contrib/<module1> web/modules/contrib/<module2> \
  --only="DrupalRector\\Drupal11\\Rector\\Deprecation\\<ClassName>" \
  --no-cache 2>&1
```

**Inspect the diff**, then reset for the next run:
```bash
git -C ~/projects/drupal-rector-test diff web/modules/contrib/
git -C ~/projects/drupal-rector-test checkout -- web/modules/contrib/
```

### 5. Report results

For each tested module, report:
```
<ModuleName> — <files_changed> file(s) changed
  Transformations: <summary of what changed>
```

### 6. Diagnose zero-match results

Common causes:

| Cause | Diagnosis | Fix |
|-------|-----------|-----|
| Untyped code | The variable calling the deprecated method has no type annotation | The rector is working correctly — untyped code is intentionally skipped to avoid false positives |
| Wrong module version | The module has already updated its code | Try an older tagged version |
| Rector class mismatch | Wrong rector class was run | Verify the rector targets the exact deprecated API used in the module |
| Rector cache | Old cache silently skips files | Already handled by `--no-cache` |
| getNodeTypes mismatch | The node type returned by the rector doesn't match the actual AST node | Read the digest rule and the actual module code to compare |

