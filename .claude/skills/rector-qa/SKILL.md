---
name: rector-qa
description: Comprehensive quality review of an existing Drupal rector. Runs four audit passes — type guards, fixture coverage, BC decision correctness, and @see URL accuracy — and produces a PASS/FAIL/WARN checklist. Use before merging a rector or when reviewing existing ones for regressions. Pass 'all' to walk the full branch type-guard checklist.
argument-hint: "<RectorClassName | all>"
allowed-tools: Read, Bash, Edit, Write, Glob
---

# Rector QA

Comprehensive four-pass quality review for a drupal-rector implementation.

## Input

`$ARGUMENTS` — one of:
- Rector class name, e.g. `ReplaceSessionManagerDeleteRector` — runs all four passes on that rector.
- `all` — walks every row marked `AT-RISK` in `.claude/skills/prompts/rector-type-specificity-checklist.md` and runs Pass 1 only, fixing each rector in sequence.

## Finding the files

```bash
find src -name "<ClassName>.php"
find tests/src -type d -name "<ClassName>"
```

Read the rector class, test class, all fixture files, and the test config.

---

## Pass 1 — Type Guard Audit

**Goal:** Every `MethodCall`, `NullsafeMethodCall`, or `PropertyFetch` node the rector handles must be guarded by an appropriate type check.

| Pattern | What to look for | Risk if missing |
|---------|-----------------|-----------------|
| `->method()` on a variable | `isObjectType($node->var, new ObjectType('FQCN'))` | Any class with this method is transformed |
| `->property` on a variable | Same `isObjectType` on `$node->var` | Any class with this property is transformed |
| `$this->method()` inside a class body | `isObjectType($node->var, ...)` or `extends`-check on enclosing `Class_` | Any class with this method is transformed |
| `ClassName::method()` static call | `isName($node->class, 'Fully\Qualified\ClassName')` — use the FQCN directly; `isObjectType` is not needed | Low risk but use FQCN, not short name |
| Global function call `foo()` | None needed | SAFE — function names are global |
| Class declaration (`class Foo extends Bar`) | Check `extends` on the `Class_` node | EXEMPT — different pattern |

**When `isObjectType` is not enough:** contrib code sometimes writes `@var Drupal\Core\Session\SessionManager` (no leading `\`). PHPStan resolves this relative to the current namespace and produces a mangled class name like `Vendor\Module\Drupal\Core\Session\SessionManager` that `isObjectType` won't match. Add a fallback using `getType($node)->getObjectClassNames()` with `str_ends_with()`:

```php
private function isSessionManagerType(Node\Expr $node): bool
{
    if ($this->isObjectType($node, new ObjectType('Drupal\Core\Session\SessionManagerInterface'))) {
        return true;
    }
    // Fallback for @var without leading \ (PHPStan mangles the class name)
    foreach ($this->getType($node)->getObjectClassNames() as $className) {
        if (str_ends_with($className, '\\Drupal\\Core\\Session\\SessionManagerInterface')) {
            return true;
        }
    }
    return false;
}
```

Only add this fallback when real-world testing shows `isObjectType` silently misses a valid case. See `ReplaceSessionManagerDeleteRector` for the reference implementation.

**Steps:**

1. Read the rector source.
2. Identify the node types from `getNodeTypes()`.
3. For each MethodCall/NullsafeMethodCall/PropertyFetch handler in `refactor()`:
   - Is there an `isObjectType($node->var, new ObjectType('FQCN'))` guard?
4. Classify:
   - **SAFE** — correct type guard present, or targets global functions/constants only
   - **AT-RISK** — matches name without type guard
   - **EXEMPT** — operates on class declaration, checks parent class

**Output:** `Pass 1: [SAFE|AT-RISK|EXEMPT] — <reason>`

**If AT-RISK:** Apply the fix (see patterns below), then update `.claude/skills/prompts/rector-type-specificity-checklist.md`:

```bash
grep -n "<ClassName>" .claude/skills/prompts/rector-type-specificity-checklist.md
```

Update the verdict column from `⚠️ AT-RISK` to `✅ SAFE` after fixing.

### Finding the right class/interface

Look up the FQCN in `repos/drupal-core` (run `bash .claude/scripts/setup-repos.sh` if absent):

```bash
grep -rn "function <methodName>\|property \$<propertyName>" repos/drupal-core/core --include="*.php" -l | head -5
```

Prefer the *interface* over the concrete class — it catches all implementations.

### Fix pattern

```php
// Before — matches any ->save() call:
if (!$this->isName($node->name, 'save')) {
    return null;
}

// After — only matches Config::save():
if (!$this->isName($node->name, 'save')) {
    return null;
}
if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Config\Config'))) {
    return null;
}
```

Always add the `isObjectType` check *after* the name check so the heavier type resolution only runs when the name already matches.

### Stub pattern

If the class is not already in `stubs/`, create a minimal stub:

```php
<?php
declare(strict_types=1);
namespace Drupal\Some\Namespace;

if (class_exists(\Drupal\Some\Namespace\ClassName::class)) {
    return;
}

class ClassName {}   // or: interface InterfaceName {}
```

Place it at `stubs/Drupal/Some/Namespace/ClassName.php`, then run `composer dump-autoload`.

### Fixture update after adding a type guard

- For a variable: add `/** @var \Fully\Qualified\Interface $var */` above the call.
- For `$this`: wrap the code in a class that `extends` or `implements` the target type.
- Add a `no_change_unrelated.php.inc` fixture showing an untyped or wrong-typed caller is left unchanged.

---

## Bulk mode (`all`)

When `$ARGUMENTS` is `all`, open `.claude/skills/prompts/rector-type-specificity-checklist.md` and work through every row marked `⚠️ AT-RISK`, one by one. For each:

1. Locate the rector source with `find src -name "<ClassName>.php"`.
2. Apply the Pass 1 steps above.
3. Fix any AT-RISK rectors and tick the checklist row.

Do not run the other three passes in bulk mode.

---

## Pass 2 — Fixture Coverage Audit

**Goal:** Fixtures should cover the happy path, no-change cases, and edge cases.

**Steps:**

1. List all fixture files:
   ```bash
   find tests/src/Drupal11/Rector/Deprecation/<ClassName>/fixture -name "*.php.inc"
   find tests/src/Drupal11/Rector/Deprecation/<ClassName>/fixture-below-version -name "*.php.inc" 2>/dev/null
   ```

2. Check for required coverage:

| Fixture | Required when | Status |
|---------|--------------|--------|
| `fixture/basic.php.inc` | Always | ✅/❌ |
| `fixture/no_change_unrelated.php.inc` | Always                                          | ✅/❌ |
| `fixture-below-version/basic.php.inc` | Rector extends `AbstractDrupalCoreRector` | ✅/❌ |
| Edge-case fixtures | Rector has conditional branches in `refactor()` | ✅/❌/N/A |

3. For `no_change_unrelated.php.inc`: verify the before and after sections are **identical** (the rector must NOT change untyped code).

4. For `fixture-below-version/basic.php.inc`: verify the before and after sections are **identical** (BC mode suppresses the transformation).

**Output:** `Pass 2: [PASS|WARN] — <missing fixtures list or "all fixtures present">`

**If WARN:** Propose missing fixture content and add it.

---

## Pass 3 — BC Decision Audit

**Goal:** The base class (`AbstractRector` vs `AbstractDrupalCoreRector`) must match the Step 4 classification from `.claude/skills/prompts/digest-to-rector-prompt.md`.

**Steps:**

1. Read the rector class docblock and header to find `extends AbstractRector` or `extends AbstractDrupalCoreRector`.

2. Re-run the Step 4 decision:
   - **Q1:** What node types does `getNodeTypes()` return?
   - **Q2:** Is the transformation Expr → Expr?
     - Old node is `Node\Expr` if: `FuncCall`, `MethodCall`, `StaticCall`, `NullsafeMethodCall`, `New_`, `Array_`, `ClassConstFetch`, `String_`, etc.
     - New node (what `refactor()` or `refactorWithConfiguration()` returns) must also be `Node\Expr`.
   - **Q3:** Was the deprecation introduced in Drupal >= 10.1.0?
     - Check `introduced_version` in the test config or `DrupalIntroducedVersionConfiguration` usage.
     - If unclear, read `repos/drupal-digests/issues/drupal-core/<issue-number>.md`.
   - **Q4:** Does the replacement code depend on a new Drupal API?
     - Read `refactor()`/`refactorWithConfiguration()` and identify what the returned node calls or
       references (function name, class name, method name, constant).
     - Ask: could this replacement code run unchanged on a Drupal version that predates the deprecation?
     - **New Drupal API** (function/method/class introduced alongside the deprecation) → BC needed.
     - **Pure PHP or version-agnostic** (native functions, inline closures, no new Drupal symbols) → BC NOT needed.

3. Expected base class:
   - Q2 = Expr → Expr AND Q3 = version >= 10.1.0 AND **Q4 = new Drupal API** → **`AbstractDrupalCoreRector`**
   - Q4 = version-agnostic replacement (or Q2/Q3 not met) → **`AbstractRector`**

4. Compare expected vs actual.

**Output:** `Pass 3: [PASS|FAIL] — expected <base class>, found <base class>`

**If FAIL:** The base class is wrong. Propose the corrected class and note that `configure()`, `refactorWithConfiguration()`, and the test class will need updating.

---

## Pass 4 — @see URL Audit

**Goal:** The rector docblock must contain `@see` lines for **both** the Drupal.org issue node
and the change record node, so the class is findable regardless of which reference appears in
a given digest file or Drupal core deprecation notice.

**Steps:**

1. Extract all `@see` lines from the rector class:
   ```bash
   grep '@see' src/Drupal11/Rector/Deprecation/<ClassName>.php
   ```

2. Determine the issue number and change record number:

   a. **Issue number** — last numeric group in the digest filename, e.g.
      `remove-deprecated-foo-3505370.php` → issue `3505370`.

   b. **Change record number** — work through these sources in order, stopping when found:

      **Source 1 — Issue markdown (fastest):**
      ```bash
      cat repos/drupal-digests/issues/drupal-core/<issue-number>.md
      ```
      Scan for a `drupal.org/node/` link in the "Upgrade path", "Change record", or "Technical
      details" sections. A link like `[#3567879](https://www.drupal.org/node/3567879)` or
      `https://www.drupal.org/node/3567879` in those sections is the change record number.
      Also check the frontmatter for `change_record_url` or similar fields.

      **Source 2 — Drupal.org issue page (reliable):**
      Fetch `https://www.drupal.org/node/<issue-number>` and look for a
      "Change records for this issue" section or a "Related change records" block.
      Those links point directly to the change record node.

      **Source 3 — Drupal core deprecation annotation (fallback):**
      Run `bash .claude/scripts/setup-repos.sh` if `repos/drupal-core` is absent, then:
      ```bash
      grep -rn "@deprecated\|trigger_error" repos/drupal-core/core --include="*.php" \
        | grep "<methodName>\|<funcName>" | head -10
      ```
      The `@see` URL inside the `@deprecated` docblock or `trigger_error` message usually
      points to the **change record**. Verify the node number differs from the issue number
      before treating it as a CR.

   c. If the issue number and change record number are the **same** (rare), one `@see` suffices.

3. Verify the rector has **both** `@see` lines:
   - `@see https://www.drupal.org/node/<issue-number>`
   - `@see https://www.drupal.org/node/<cr-number>`

4. Flag:
   - **PASS** — both `@see` lines present (or issue == CR, so one is correct)
   - **WARN** — one `@see` present but the other is missing; add the missing line
   - **FAIL** — `@see` points to an entirely unrelated node

**If WARN/FAIL:** Add or correct the `@see` line(s) in the rector docblock. Both should appear
consecutively, issue first:

```php
 * @see https://www.drupal.org/node/<issue-number>
 * @see https://www.drupal.org/node/<cr-number>
```

**Output:** `Pass 4: [PASS|WARN|FAIL] — issue:<number> CR:<number> — <present/missing>`

---

## Final Summary

After all four passes, produce a summary checklist:

```
=== QA Summary: <ClassName> ===

Pass 1 — Type Guard:    [SAFE|AT-RISK|EXEMPT]
Pass 2 — Fixtures:      [PASS|WARN] — <note>
Pass 3 — BC Decision:   [PASS|FAIL] — <note>
Pass 4 — @see URL:      [PASS|WARN|FAIL] — issue:<n> CR:<n> — <present/missing>

Overall: [PASS — ready to merge | NEEDS FIXES — see above]
```

If any pass shows AT-RISK or FAIL, do not declare the rector ready to merge. Apply the proposed fixes and re-run the affected passes.

---

## Running on a "known good" rector

To verify the skill works correctly, run it on `ReplaceSessionManagerDeleteRector` — all four passes should be PASS/SAFE.
