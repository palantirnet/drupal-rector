---
name: rector-implement
description: Converts a single drupal-digests rule to a drupal-rector-compliant implementation. Follows .claude/skills/prompts/digest-to-rector-prompt.md steps 1–14 and adds quality gates for type guards (QG-A) and version-gating tests (QG-B). Pass the path to the digests rule file as argument.
argument-hint: "repos/drupal-digests/rector/rules/<rule-filename>.php"
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
- Step 14: Done (no commit — leave that to the reviewer)

**Do not skip or abbreviate any step.** The `.claude/skills/prompts/digest-to-rector-prompt.md` prompt is authoritative.

---

### After Step 10: Quality Gate QG-A — Type Guard Audit

For every `MethodCall`, `NullsafeMethodCall`, or `PropertyFetch` node the rector handles:

**Choose the right guard for the node type:**
- Instance method/property on a variable → `isObjectType($node->var, new ObjectType('FQCN'))` (prefer the interface over the concrete class)
- Static call `ClassName::method()` → `isName($node->class, 'Fully\Qualified\ClassName')` using the FQCN directly — `isObjectType` is not needed here
- Global function `foo()` or class constant `ClassName::CONST` → no guard needed, SAFE

1. Is the correct guard present?
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
   e. Add the guard to the rector class (after the name check).
   f. Add a `no_change_unrelated.php.inc` fixture proving untyped callers are skipped:
      - Before section: a call with no type annotation
      - After section: identical (no change)

**Note on `isObjectType` limits:** if real-world testing shows the rector silently skips valid cases, the cause is often a `@var` annotation without a leading `\` — PHPStan mangles the class into the current namespace. Add a `getType($node)->getObjectClassNames()` + `str_ends_with()` fallback (see `ReplaceSessionManagerDeleteRector::isSessionManagerType()` for the reference pattern). Only add this when a real miss is confirmed; do not add it pre-emptively.

**Global functions (FuncCall without a receiver) and class constants (ClassConstFetch) do NOT need `isObjectType()` guards — skip QG-A for these.**

---

### After QG-A: Quality Gate QG-B — Version-Gating Tests (BC-wrapped rectors only)

Apply only if the rector extends `AbstractDrupalCoreRector`.

Replace the simple test class with the full `testAboveVersion` / `testBelowVersion` form that uses
`DrupalRectorSettings::setDrupalVersion()`:

```php
<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal11\Rector\Deprecation\[ClassName];

use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;

class [ClassName]Test extends AbstractDrupalRectorTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideData')]
    public function testAboveVersion(string $filePath): void
    {
        static::getContainer()->make(DrupalRectorSettings::class)->setDrupalVersion('99.99.99');
        $this->doTestFile($filePath);
    }

    /**
     * @return \Iterator<<string>>
     */
    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture');
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideDataBelowVersion')]
    public function testBelowVersion(string $filePath): void
    {
        static::getContainer()->make(DrupalRectorSettings::class)->setDrupalVersion('1.0.0');
        $this->doTestFile($filePath);
    }

    /**
     * @return \Iterator<<string>>
     */
    public static function provideDataBelowVersion(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-below-version');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }
}
```

- `'99.99.99'` simulates a Drupal version well above any introduced version → BC wrapper fires.
- `'1.0.0'` simulates a version below every introduced version → rector skips, no change applied.

Create `tests/src/Drupal11/Rector/Deprecation/[ClassName]/fixture-below-version/basic.php.inc`:
```
<?php

[same "before" code as main fixture]
?>
-----
<?php

[same "before" code — no change, because Drupal version is below the introduced version]
?>
```

Only "transformable" fixtures (those that produce a change) need a `fixture-below-version/`
counterpart. No-change fixtures (`no_change_*.php.inc`) do not need one — they already show no
transformation.

---

### After Step 14: Record the implemented digest

`docs/implemented-digests.yml` is the **authoritative, hand-maintained** record of
which digests are implemented (keyed by issue number). It is NOT generated — append to
it directly. Do **not** run `generate-rector-index.php` to "record" the work: that
matcher is unstable and can mark this very rule `pending`. This file is what survives.

You already have ground truth in hand (issue number, class name from Step 5, digest
filename = basename of `$ARGUMENTS`). Add one entry under `digests:`, keyed by the issue
number, kept in natural issue-number order.

For a custom rector class:
```yaml
  '3577376':
    status: implemented
    phase: '2'
    class: ReplaceSessionManagerDeleteRector
    digest_file: replace-deprecated-sessionmanager-delete-with-3577376.php
    note: 'Implemented in <branch/PR>.'   # optional
```

For a config-only rule (Step 4b path — no custom class), use `status: config-only` and
omit `class`:
```yaml
  '1685492':
    status: config-only
    phase: '1a'
    digest_file: replace-deprecated-twig-extension-and-twig-render-template-1685492.php
    note: 'Handled via drupal-11.3-deprecations.php.'
```

If an entry for that issue already exists, update it in place rather than duplicating.
No regeneration step — the edit is the record.

---

### After the index update: Run rector-qa

Read `.claude/skills/rector-qa/SKILL.md` and execute all four passes for `[ClassName]`.

Apply any fixes the QA reveals. Do not declare the implementation complete until rector-qa reports **Overall: PASS**.

---

## Pre-flight Checklist

Before declaring the implementation complete, verify all items from `.claude/skills/prompts/digest-to-rector-prompt.md`'s final checklist, plus:

- [ ] QG-A: `isObjectType()` guard present for all MethodCall/PropertyFetch nodes (or explicitly not needed)
- [ ] QG-A: `no_change_unrelated.php.inc` fixture exists if a type guard was added
- [ ] QG-B: `testAboveVersion()` + `testBelowVersion()` (with `DrupalRectorSettings::setDrupalVersion`) and `fixture-below-version/basic.php.inc` present if BC-wrapped
- [ ] `vendor/bin/phpunit tests/src/Drupal11/Rector/Deprecation/[ClassName]/` passes
- [ ] `ddev composer phpstan` reports no new errors
- [ ] `ddev composer fix-style` produces no changes
- [ ] rector-qa reports **Overall: PASS** (all four passes green)
- [ ] Digest recorded in `docs/implemented-digests.yml` (append-only source of truth)

## Quick Reference: Phase 1 (config-only) path

If Step 4b determines a generic rector handles this rule, follow the "config-only" path in `.claude/skills/prompts/digest-to-rector-prompt.md` Step 4b instead of generating a custom class. No custom PHP class is written — only a config entry and fixture are added.
