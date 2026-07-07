---
name: contrib-search
description: Search real Drupal contrib code for a pattern (deprecated method/function/constant/property) via the api.tresbien.tech JSON index. Default quick mode answers "does this occur in real contrib, and how widely?"; full mode additionally filters to D11-compatible modules ranked by install count, ready to live-test. Pass the symbol or a rector class/issue number.
argument-hint: "<symbol | RectorClassName | issue-number> [--full]"
allowed-tools: Read, Bash, Glob
---

# Contrib Search

Search the `api.tresbien.tech` code index for real contrib usages of a pattern.
Two exit points:

- **Quick mode (default)** — is this pattern out there, and how widespread? Report
  hit count + a couple of decoded example call sites. Nothing installed, nothing ranked.
  This is the "is the concern real?" lookup you reach for during review / QA / implement.
- **Full mode (`--full`)** — everything quick mode does, then filter hits to
  D11-compatible modules ranked by install count, producing a module list ready to
  live-test. This is what `rector-live-test` consumes.

Scope is **contrib only** — every query includes `-r:drupal` to exclude core. This skill
does not search core; for a core symbol's definition/signature use `rg repos/drupal-core`.

## Input

`$ARGUMENTS` — one of:
- A raw symbol/pattern: `->setCacheKey\(`, `_filter_autop\(`, `SomeClass::CONST`
- A rector class name: `ReplaceSessionManagerDeleteRector`
- An issue number: `3577376`

Append `--full` to run full mode.

## 1. Resolve the pattern

If given a raw pattern, use it directly (skip to step 2).

If given a **rector class name**, find and read the source:
```bash
find src -name "<ClassName>.php"
```
If given an **issue number**, look up the class in `docs/rector-index.yml` (regenerate via
the `rector-discover` skill if missing/stale), then find the source.

From the rector source, extract the pattern to search for:
- Deprecated method/function/constant name (from `isName()`, constants, or `FUNCTION_MAP`)
- Deprecated class/interface name (from `isObjectType()` guards)

## 2. Search the index

Base URL:
```
https://api.tresbien.tech/v1/search?q=<urlencoded_query>&num=<max_results>
```

**Always include `-r:drupal`** to exclude Drupal core (use `-r:drupal`, NOT `-r:core`).

**Regex escaping:** the query is a regex. Escape `(` as `\(` — an unescaped `(` causes a
parse error and returns **HTTP 418**.

Query construction:
- Method call: `-r:drupal ->methodName\(`
- Function call: `-r:drupal functionName\(`
- Class constant: `-r:drupal ClassName::CONSTANT_NAME`
- Property access: `-r:drupal ->propertyName`

Optional filters:
- `f:\.php$` — PHP files only (add `f:\.module$` if the pattern may live in `.module` files)
- `-f:test` — exclude test files
- `lang:php` — PHP language filter
- `case:yes` — force case-sensitive match

Example — `_filter_autop(` in contrib PHP files, excluding tests:
```bash
curl -s "https://api.tresbien.tech/v1/search?q=-r%3Adrupal+_filter_autop%5C%28+-f%3Atest&num=20" \
  | jq -r '.Result.Files[] | "\(.Repository)\t\(.FileName)\t\(.Branches | join(","))"'
```

Response is JSON, `Result.Files[]`, each entry:
- `.Repository` — module/project name (use directly, no path parsing)
- `.FileName` — file path within the repo
- `.Branches[]` — branch(es) the match is on
- `.ChunkMatches[].Content` — base64-encoded matched line(s)

Decode a matched line to see real code context:
```bash
echo "<base64string>" | base64 -d
```

**Never loop over individual repos.** To restrict to a known set, use regex alternation:
`r:^(module1|module2|module3)$`.

## 3. Report — quick mode (default)

Report:
- **Hit count** — number of `Result.Files[]` entries (note if truncated at `num`).
- **Distinct modules** — unique `.Repository` values.
- **2–3 decoded example call sites** — `.Repository` / `.FileName` + the base64-decoded line.
- A one-line verdict: is the pattern present in real contrib, and roughly how widespread?

This is enough to settle "is this concern real?" — stop here unless `--full` was passed.
Per project convention, a rector false-positive isn't a BLOCKER unless its trigger pattern
actually occurs in real contrib code; this search is how you check.

## 4. Filter to D11-compatible + rank — full mode (`--full`)

Batch-check every found module with the repo listing API. It returns
`RawConfig."drupal-core"` (branch-keyed compat strings) and `RawConfig."drupal-usage"`
(install counts per branch):

```bash
MODULES='module1|module2|module3'   # pipe-separated list of .Repository values from step 2
curl -s "https://api.tresbien.tech/v1/search/repo" \
  | jq -r --arg mods "$MODULES" \
    '.List.Repos[]
     | select(.Repository.Name | test($mods))
     | select(.Repository.RawConfig."drupal-core" // "" | test("\\^11"))
     | [.Repository.Name,
        .Repository.RawConfig."drupal-core",
        .Repository.RawConfig."drupal-usage"] | @tsv'
```

- `drupal-core` looks like `"1.x:^10 || ^11;2.x:^11"` — keep modules where any branch
  entry includes `^11`.
- `drupal-usage` looks like `"1.x:4521;2.x:312"` — **prefer higher install counts** for
  better real-world coverage.

Report the D11-compatible modules ranked by install count, each with its file path(s), as a
list ready to feed to `rector-live-test`.

If no D11-compatible modules are found, report:
```
No D11-compatible contrib modules found for <pattern>.
Try the UI: https://search.tresbien.tech
```
