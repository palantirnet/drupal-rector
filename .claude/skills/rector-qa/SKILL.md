---
name: rector-qa
description: Comprehensive quality review of an existing Drupal rector. Runs four audit passes — type guards, fixture coverage, BC decision correctness, and @see URL accuracy — and produces a PASS/FAIL/WARN checklist. Use before merging a rector or when reviewing existing ones for regressions.
argument-hint: "<RectorClassName>"
allowed-tools: Read, Bash, Edit, Write, Glob
---

# Rector QA

Comprehensive four-pass quality review for a drupal-rector implementation.

## Input

`$ARGUMENTS` — rector class name, e.g. `ReplaceSessionManagerDeleteRector`.

## Finding the files

```bash
find src -name "<ClassName>.php"
find tests/src -type d -name "<ClassName>"
```

Read the rector class, test class, all fixture files, and the test config.

---

## Pass 1 — Type Guard Audit

**Goal:** Every `MethodCall`, `NullsafeMethodCall`, or `PropertyFetch` node the rector handles must be guarded by `isObjectType()`. See `~/.claude/skills/rector-type-check-review/SKILL.md` for the full pattern reference.

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

**If AT-RISK:** Propose the fix (see `rector-type-check-review` skill for exact fix pattern). Apply it and update `docs/rector-type-specificity-checklist.md`:

```bash
# Find the row for this rector in the checklist
grep -n "<ClassName>" docs/rector-type-specificity-checklist.md
```

Update the verdict column from `⚠️ AT-RISK` to `✅ SAFE` after fixing.

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
| `fixture/no_change_unrelated.php.inc` | Rector uses `isObjectType()` | ✅/❌ |
| `fixture-below-version/basic.php.inc` | Rector extends `AbstractDrupalCoreRector` | ✅/❌ |
| Edge-case fixtures | Rector has conditional branches in `refactor()` | ✅/❌/N/A |

3. For `no_change_unrelated.php.inc`: verify the before and after sections are **identical** (the rector must NOT change untyped code).

4. For `fixture-below-version/basic.php.inc`: verify the before and after sections are **identical** (BC mode suppresses the transformation).

**Output:** `Pass 2: [PASS|WARN] — <missing fixtures list or "all fixtures present">`

**If WARN:** Propose missing fixture content and add it.

---

## Pass 3 — BC Decision Audit

**Goal:** The base class (`AbstractRector` vs `AbstractDrupalCoreRector`) must match the Step 4 classification from `docs/digest-to-rector-prompt.md`.

**Steps:**

1. Read the rector class docblock and header to find `extends AbstractRector` or `extends AbstractDrupalCoreRector`.

2. Re-run the Step 4 decision:
   - **Q1:** What node types does `getNodeTypes()` return?
   - **Q2:** Is the transformation CallLike → CallLike?
     - Old node is CallLike if: `FuncCall`, `MethodCall`, `StaticCall`, `NullsafeMethodCall`, `New_`
     - New node (what `refactor()` or `refactorWithConfiguration()` returns) is CallLike if: same list
   - **Q3:** Was the deprecation introduced in Drupal >= 10.1.0?
     - Check `introduced_version` in the test config or `DrupalIntroducedVersionConfiguration` usage.
     - If unclear, read `~/projects/drupal-digests/issues/drupal-core/<issue-number>.md`.

3. Expected base class:
   - Q2 = CallLike → CallLike AND Q3 = version >= 10.1.0 → **`AbstractDrupalCoreRector`**
   - Otherwise → **`AbstractRector`**

4. Compare expected vs actual.

**Output:** `Pass 3: [PASS|FAIL] — expected <base class>, found <base class>`

**If FAIL:** The base class is wrong. Propose the corrected class and note that `configure()`, `refactorWithConfiguration()`, and the test class will need updating.

---

## Pass 4 — @see URL Audit

**Goal:** The `@see` URL in the rector class docblock should point to the correct Drupal.org node.

**Steps:**

1. Extract the `@see` URL from the rector class:
   ```bash
   grep '@see' src/Drupal11/Rector/Deprecation/<ClassName>.php
   ```

2. Determine the issue number and change record number:
   - The digest filename contains the **issue number** (last numeric group).
   - `~/projects/drupal-digests/issues/drupal-core/<issue-number>.md` contains the change record link if known.
   - Alternatively, search `~/projects/drupal-core` for the deprecated function/method:
     ```bash
     grep -rn "@deprecated in drupal:" ~/projects/drupal-core/core --include="*.php" | grep "<methodName>" | head -5
     ```
     The `@see` in the Drupal core deprecation notice usually points to the **change record**.

3. Expected `@see` URL:
   - Rector should point to the **issue node** if that's what the digest file uses.
   - OR the **change record node** if the Drupal core source cites it.
   - Flag if the `@see` points to a node that's clearly wrong (e.g., points to a different, unrelated issue).

4. Check URL validity by comparing the node number with known data:
   - Issue URL: `https://www.drupal.org/node/<issue-number>` — from digest filename
   - CR URL: `https://www.drupal.org/node/<cr-number>` — from drupal-core deprecation annotation

**Output:** `Pass 4: [PASS|WARN] — <URL> [matches issue|matches CR|MISMATCH: should be <correct URL>]`

**If WARN/FAIL:** Propose the corrected `@see` URL and apply it.

---

## Final Summary

After all four passes, produce a summary checklist:

```
=== QA Summary: <ClassName> ===

Pass 1 — Type Guard:    [SAFE|AT-RISK|EXEMPT]
Pass 2 — Fixtures:      [PASS|WARN] — <note>
Pass 3 — BC Decision:   [PASS|FAIL] — <note>
Pass 4 — @see URL:      [PASS|WARN] — <note>

Overall: [PASS — ready to merge | NEEDS FIXES — see above]
```

If any pass shows AT-RISK or FAIL, do not declare the rector ready to merge. Apply the proposed fixes and re-run the affected passes.

---

## Running on a "known good" rector

To verify the skill works correctly, run it on `ReplaceSessionManagerDeleteRector` — all four passes should be PASS/SAFE.
