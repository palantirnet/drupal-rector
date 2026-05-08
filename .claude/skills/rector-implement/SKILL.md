---
name: rector-implement
description: Converts a single drupal-digests rule to a drupal-rector-compliant implementation. Follows .claude/skills/prompts/digest-to-rector-prompt.md steps 1–14 and adds quality gates for type guards (QG-A) and version-gating tests (QG-B). Pass the path to the digests rule file as argument.
argument-hint: "~/projects/drupal-digests/rector/rules/<rule-filename>.php"
allowed-tools: Read, Write, Edit, Bash, Glob
---

# Rector Implement

Convert a single drupal-digests rule into a drupal-rector–compliant implementation with tests.

## Input

`$ARGUMENTS` must be the path to a drupal-digests rule file, e.g.:
```
repos/drupal-digests/rector/rules/replace-deprecated-sessionmanager-delete-with-3577376.php
```

If `repos/drupal-digests` does not exist yet, run `bash .claude/scripts/setup-repos.sh` first.

## Steps

### Step 0 — Try a recipe first

Before doing anything else:

1. Read the digest file.
2. Read `.claude/skills/prompts/recipes/RECIPES.md` and answer the routing questions.
3. If a recipe matches, read that recipe file and follow it **instead of Steps 1–14 below**.
   The recipe is self-contained and includes quality checks and commit instructions.
4. If no recipe matches, continue with Steps 1–14.

Available recipes:
- `func-to-class-service-bc.md` — single FuncCall → `Fqcn::class` service method (BC-wrapped)
- `func-to-class-service-bc-multi.md` — multiple FuncCalls → same `Fqcn::class` service (BC-wrapped)
- `config-only-template.md` — all config-only patterns (FunctionToServiceRector, FunctionToStaticRector, etc.)

---

### Steps 1–14: Follow the canonical conversion workflow (fallback)

Read `.claude/skills/prompts/digest-to-rector-prompt.md` completely and execute steps 1–14 as written there.

The canonical prompt covers:
- Step 1: Confirm input and extract class name, node types, refactor logic, code samples, issue number
- Step 2: Read the companion issue markdown
- Step 3: Fetch from Drupal.org if Step 2 was insufficient
- Step 4: Classify the rule (BC decision) — `AbstractRector` vs `AbstractDrupalCoreRector`
- Step 4b: Check for existing generic rectors BEFORE writing a custom class
- Step 5: Derive class name and file paths
- Step 6: Generate the rule class
- Step 7: Generate the fixture file
- Step 8: Generate the test class
- Step 9: Generate the test config
- Step 10: Write all files
- Step 11: Fix code style (`ddev composer fix-style`)
- Step 12: Run static analysis (`ddev composer phpstan`)
- Step 13: Run the test (`vendor/bin/phpunit tests/src/Drupal11/Rector/Deprecation/[ClassName]/`)
- Step 14: Commit

**Do not skip or abbreviate any step.** The `.claude/skills/prompts/digest-to-rector-prompt.md` prompt is authoritative.

---

### After Step 10: Quality Gate QG-A — Type Guard Audit

For every `MethodCall`, `NullsafeMethodCall`, or `PropertyFetch` node the rector handles:

1. Is an `isObjectType()` guard present that constrains the owning class/interface?
2. If missing:
   a. Find the owning interface/class in the Drupal core source (`repos/drupal-core`). If absent, run `bash .claude/scripts/setup-repos.sh` first.
      ```bash
      grep -rn "function <methodName>\|property \$<propertyName>" repos/drupal-core/core --include="*.php" -l | head -5
      ```
   b. Check whether a stub exists:
      ```bash
      find stubs/ -name "*.php" | xargs grep -l "class <ClassName>\|interface <InterfaceName>" 2>/dev/null
      ```
   c. If no stub: create a minimal stub at `stubs/Drupal/Some/Namespace/ClassName.php`:
      ```php
      <?php
      declare(strict_types=1);
      namespace Drupal\Some\Namespace;
      if (class_exists(\Drupal\Some\Namespace\ClassName::class)) {
          return;
      }
      interface ClassName {}
      ```
   d. Run `composer dump-autoload` to register the stub.
   e. Add the `isObjectType()` guard to the rector class (after the name check).
   f. Add a `no_change_unrelated.php.inc` fixture proving untyped callers are skipped:
      - Before section: a call with no type annotation
      - After section: identical (no change)

**Global functions (FuncCall without a receiver) and class constants (ClassConstFetch) do NOT need `isObjectType()` guards — skip QG-A for these.**

---

### After QG-A: Quality Gate QG-B — Version-Gating Tests (BC-wrapped rectors only)

Apply only if the rector extends `AbstractDrupalCoreRector`.

1. Add `testAboveVersion()` method to the test class:
   ```php
   public function testAboveVersion(): void
   {
       $this->doTestFile(__DIR__ . '/fixture/basic.php.inc');
   }
   ```
   This is the existing test — rename if needed or leave it.

2. Add `testBelowVersion()` method. Use a version just below the rector's `introduced_version` (e.g., if introduced in `11.4.0`, use `11.3.0`):
   ```php
   public function testBelowVersion(): void
   {
       AbstractDrupalCoreRector::setVersionOverride('<major>.<minor-1>.0');
       try {
           $this->doTestFile(__DIR__ . '/fixture-below-version/basic.php.inc');
       } finally {
           AbstractDrupalCoreRector::setVersionOverride(null);
       }
   }
   ```

3. Create `tests/src/Drupal11/Rector/Deprecation/[ClassName]/fixture-below-version/basic.php.inc`:
   ```
   <?php

   [same "before" code as main fixture]
   ?>
   -----
   <?php

   [same "before" code — no change, because Drupal version is below the introduced version]
   ?>
   ```

---

### After Step 14: Update the index (if it exists)

```bash
if [ -f docs/rector-index.yml ]; then
  php .claude/scripts/generate-rector-index.php
fi
```

This marks the newly implemented rule as `implemented` in the index.

---

## Pre-flight Checklist

Before declaring the implementation complete, verify all items from `.claude/skills/prompts/digest-to-rector-prompt.md`'s final checklist, plus:

- [ ] QG-A: `isObjectType()` guard present for all MethodCall/PropertyFetch nodes (or explicitly not needed)
- [ ] QG-A: `no_change_unrelated.php.inc` fixture exists if a type guard was added
- [ ] QG-B: `testBelowVersion()` and `fixture-below-version/basic.php.inc` present if BC-wrapped
- [ ] `vendor/bin/phpunit tests/src/Drupal11/Rector/Deprecation/[ClassName]/` passes
- [ ] `ddev composer phpstan` reports no new errors
- [ ] `ddev composer fix-style` produces no changes

## Quick Reference: Phase 1 (config-only) path

If Step 4b determines a generic rector handles this rule, follow the "config-only" path in `.claude/skills/prompts/digest-to-rector-prompt.md` Step 4b instead of generating a custom class. No custom PHP class is written — only a config entry and fixture are added.
