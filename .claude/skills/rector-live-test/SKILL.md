---
name: rector-live-test
description: Runs a rector against real D11-compatible contrib modules to verify it transforms real-world code correctly, then captures the PHPStan deprecation message it covers. Delegates finding the modules to the contrib-search skill. Pass rector class name or issue number as argument.
argument-hint: "<RectorClassName or issue-number>"
allowed-tools: Read, Bash, Glob, Skill
---

# Rector Live Test

Find real contrib modules that use the deprecated API a rector targets, then run the rector against them to verify it transforms real-world code correctly.

## Input

`$ARGUMENTS` — either:
- Rector class name: `ReplaceSessionManagerDeleteRector`
- Issue number: `3577376`

## Steps

### 1. Find D11-compatible contrib modules that exercise the rector

Invoke the **`contrib-search`** skill in **full mode**, passing the rector class name or
issue number:

```
contrib-search <ClassName or issue-number> --full
```

It resolves the rector's deprecated symbol, searches `api.tresbien.tech`, filters to
D11-compatible modules, and returns a list ranked by install count with the file path(s)
each match lives in. Use that list as the modules to test in the following steps.

If it reports no D11-compatible modules, stop and report that — there is nothing to
live-test.

### 2. Run the rector

**Check if the DDEV test project exists:**
```bash
ls ~/projects/drupal-rector-test/.ddev 2>/dev/null && echo "exists" || echo "not found"
```

**If not found**, run the one-time setup (takes ~10 minutes):
```bash
bash .claude/skills/rector-live-test/setup-rector-test.sh
```
This creates a Drupal 11 site at `~/projects/drupal-rector-test` with a broad set of
contrib modules pre-installed.

**If found**, check whether DDEV is running and start it only if needed:
```bash
cd ~/projects/drupal-rector-test
DDEV_STATUS=$(ddev status --json-output 2>/dev/null | python3 -c "import json,sys; print(json.load(sys.stdin).get('raw',{}).get('status','stopped'))" 2>/dev/null || echo "stopped")
[ "$DDEV_STATUS" = "running" ] || ddev start -y
```

**If a module found in step 1 is not pre-installed**, add it before running:
```bash
cd ~/projects/drupal-rector-test
ddev composer require drupal/<module> --no-interaction
```

**Resolve the FQCN** based on where the rector source lives:
- `src/Drupal11/…` → `DrupalRector\Drupal11\Rector\Deprecation\<ClassName>`
- `src/Drupal10/…` → `DrupalRector\Drupal10\Rector\Deprecation\<ClassName>`
- `src/Rector/…`   → `DrupalRector\Rector\Deprecation\<ClassName>`

**Write a minimal config file** — do NOT use `--only` (it loads the project's default rector.php
which may be broken) and do NOT omit `fileExtensions` (Rector only processes `.php` by default,
silently skipping `.module`, `.install`, etc.):

```bash
cat > ~/projects/drupal-rector-test/rector-live-test.php << 'RECTOR_EOF'
<?php
declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\<ClassName>;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->fileExtensions(['php', 'module', 'theme', 'install', 'profile', 'inc']);
    $rectorConfig->bootstrapFiles([
        __DIR__.'/vendor/palantirnet/drupal-rector/config/drupal-phpunit-bootstrap-file.php',
    ]);
    $rectorConfig->ruleWithConfiguration(<ClassName>::class, [
        new DrupalIntroducedVersionConfiguration('<introduced-version>'),
    ]);
};
RECTOR_EOF
```

**Run rector** against the found modules:
```bash
cd ~/projects/drupal-rector-test
ddev exec -d /var/www/html \
  vendor/bin/rector process \
  web/modules/contrib/<module1> web/modules/contrib/<module2> \
  --config rector-live-test.php \
  --clear-cache 2>&1
```

**Inspect the diff**, then reset and clean up:
```bash
git -C ~/projects/drupal-rector-test diff web/modules/contrib/
git -C ~/projects/drupal-rector-test checkout -- web/modules/contrib/
rm ~/projects/drupal-rector-test/rector-live-test.php
```

If the contrib modules are not git-tracked in the test project, `git checkout` won't restore them.
Use `ddev composer reinstall drupal/<module1> drupal/<module2> --no-interaction` instead.

### 3. Capture the PHPStan deprecation message

While the contrib module is still installed and the pre-transform code is on
disk, run PHPStan against the file the rector matched and capture the
deprecation message PHPStan emits for the targeted symbol. This is the literal
string the rector "covers", and is what upgrade_status's
`DeprecationAnalyzer::isRectorCovered()` does an exact string match against
(after a small set of normalizations — see below).

```bash
cd ~/projects/drupal-rector-test
ddev exec vendor/bin/phpstan analyse \
  web/modules/contrib/<module>/<file_the_rector_matched>.php \
  --level=max --no-progress --error-format=raw 2>&1 \
  | grep -i "deprecated.*<short_symbol_name>"
```

If no contrib file matched (or the symbol is already fully removed from
installed core so PHPStan emits "not found" rather than a deprecation), fall
back to a synthetic probe — see the `rector-extract-phpstan-error` skill's
"Synthetic probe" section for templates.

**Normalize and store.** Pipe the raw message through the normalizer (which
applies the three transforms upgrade_status applies before its `in_array()`
lookup — whitespace collapse, `: in` → `. Deprecated in`, leading `\Drupal`
strip):

```bash
ddev exec php scripts/normalize-phpstan-message.php "<raw multi-line message>"
# or:
printf '<raw message>' | ddev exec php scripts/normalize-phpstan-message.php
```

Add the normalized string to the rector source:

- **Custom rector class** (`src/Drupal*/Rector/Deprecation/<ClassName>.php`)
  — add or extend the `public const PHPSTAN_MESSAGES` array. One element per
  distinct call shape the rector handles.

  ```php
  public const PHPSTAN_MESSAGES = [
      'Call to deprecated method foo() of class Drupal\Bar. Deprecated in drupal:11.4.0 ...',
  ];
  ```

- **Config-only registration** (`config/drupal-*/drupal-*.N-deprecations.php`)
  — add a `// PHPSTAN_MESSAGES <RectorShortName>:` comment block immediately
  above the `ruleWithConfiguration(...)` call, one message per `//` line:

  ```php
  // PHPSTAN_MESSAGES FunctionToServiceRector:
  //   Call to deprecated function foo(). Deprecated in drupal:11.4.0 and is removed from drupal:12.0.0. Use Drupal\Bar::baz() instead.
  $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [ /* ... */ ]);
  ```

Then regenerate the flat registry:

```bash
ddev exec php scripts/generate-coverage-registry.php
```

This writes `docs/coverage-registry.php` (a `return [...]` file mapping
rector short name → list of normalized messages). The registry is the
artifact a future upgrade_status PR will consume to replace its hardcoded
`$rector_covered` array.

If PHPStan emits no deprecation for the symbol — symbol present but not
annotated `@deprecated`, or already fully removed — record a `TODO
PHPSTAN_MESSAGES <RectorShortName>:` comment with the reason instead of
guessing the message text. Do **not** synthesize the string from the
`@deprecated` docblock by hand: PHPStan's exact wording differs between
"Call to deprecated method", "Instantiation of deprecated class", "Class X
implements deprecated interface", etc.

### 4. Report results

For each tested module, report:
```
<ModuleName> — <files_changed> file(s) changed
  Transformations: <summary of what changed>
```

For every module with **zero changes**, do not just say "no match" — always show the actual
code and explain why. See step 5.

### 5. Diagnose zero-match results

For **every** module that produced no changes, you must:

1. **Find the exact call site:**
   ```bash
   grep -n "<deprecatedMethod>" ~/projects/drupal-rector-test/web/modules/contrib/<module>/<file>
   ```

2. **Show the surrounding code** (±8 lines):
   ```bash
   sed -n '<start>,<end>p' ~/projects/drupal-rector-test/web/modules/contrib/<module>/<file>
   ```

3. **Diagnose** by reading the code and identifying the cause from the table below.

4. **Report** the code snippet and diagnosis inline — do not skip this even if the cause seems obvious.

**Common causes:**

| Cause | How to spot it | Verdict |
|-------|---------------|---------|
| Untyped receiver | No `@var` annotation and no type-hinted parameter for the variable | Rector is correct to skip — would cause false positives on unrelated classes |
| Chained call, return type unresolvable | `$foo->something()->getOriginalClass()` where `something()` has no known return type | Rector correctly skips — add phpstan-drupal or a stub to fix |
| Broken `use` import | File imports a class from a module that isn't installed | PHPStan can't resolve the import, degrades type inference for the whole file |
| `.module` file silently skipped | File extension is `.module`, `.install`, etc. | Config is missing `fileExtensions()` — this should not happen if step 2 was followed |
| Module already updated | The call site no longer uses the deprecated API | Expected — the module has already migrated |
| Wrong rector class | The rector targets a different method/function | Verify the rector's `isName()` matches the actual call in the module |

