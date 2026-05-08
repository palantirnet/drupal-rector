---
title: "fix: Rector test failures and missed triggers from rector-test.log analysis"
type: fix
status: active
date: 2026-05-08
---

# fix: Rector test failures and missed triggers from rector-test.log analysis

## Summary

Analysis of `rector-test.log` from `drupal-rector-test2` revealed two behavioral failures (rectors that run but do the wrong thing), five rectors that silently produce zero changes against plausible targets, and one rector that runs twice due to set duplication. This plan tracks investigation and fixes for each item.

---

## Problem Frame

The test log showed no hard PHP errors or test failures, but several rectors have either:
1. **False positives** — they modify code they should not touch
2. **Silent misses** — they match a target module but apply zero changes, suggesting a pattern mismatch in the node visitor

---

## Issue Inventory

### P1 — Behavioral Failures (rector runs, does wrong thing)

#### F1: `RemoveModuleHandlerAddModuleCallsRector` — false positive, deletes unrelated method body

**File affected:** `web/modules/contrib/config_track/src/Extension/ModuleHandler.php`

**What happens:** The rector is supposed to remove deprecated `addModule()` and `addProfile()` call delegations. It correctly empties those two methods, but it also blanks out the body of `loadAllIncludes()` — a method outside its stated scope.

**Root cause:** The `config_track` source has a pre-existing bug: `loadAllIncludes()` delegates to `$this->innerModuleHandler->addProfile(...)` instead of the correct method. The rector's node visitor sees an `addProfile()` call and removes it regardless of which method it lives in.

**Fix needed:** Scope the node visitor to only remove `addProfile()`/`addModule()` calls that are the *sole statement* in methods named `addProfile` or `addModule` (i.e. delegation stubs), not arbitrary calls to those methods elsewhere.

**Status:** [ ] Investigated [ ] Fixed [ ] Test added

---

#### ~~F2: `ReplaceDateTimeRangeConstantsRector` — name/behavior mismatch~~ CLOSED — not a bug

The rector deliberately handles two patterns from the same change record ([drupal.org/node/3574901](https://www.drupal.org/node/3574901)):
1. `DateTimeRangeConstantsInterface::BOTH/START_DATE/END_DATE` → `DateTimeRangeDisplayOptions` enum
2. `datetime_type_field_views_data_helper()` → `\Drupal::service('datetime.views_helper')->buildViewsData(...)`

Both are documented in the docblock and `getRuleDefinition()`. The log showed branch 2 firing on `scheduler_field` because that module uses the function but not the constants. Correct behaviour. Class name is slightly narrow but not incorrect.

---

### P2 — Silent Misses (zero changes on plausible targets)

#### M1: `RemoveUpdaterPostInstallMethodsRector` — `gnode_request` (28 files), 0 changes

**Target pattern:** `postInstall()` / `postUpdateFileTransfer()` methods from `UpdaterInterface`

**Suspicion:** The class hierarchy check (resolving whether a class `implements UpdaterInterface`) may fail in the test Drupal installation if the interface is not autoloadable. The module was specifically matched as a target by the setup script, so the code likely exists.

**Investigation steps:**
1. Grep `gnode_request` for `UpdaterInterface`, `postInstall`, `postUpdateFileTransfer`
2. Check if the rector's condition requires the class to be resolvable via `instanceof` / reflection

**Status:** [ ] Investigated [ ] Fixed [ ] Test added

---

#### ~~M2: `ReplaceCommentManagerGetCountNewCommentsRector` — `history` (23 files), 0 changes~~ CLOSED — true negative

The rector requires the receiver to be typed as `Drupal\comment\CommentManagerInterface`. Every `getCountNewComments()` call in `history` is already on `HistoryManager` (the new API) — the module has fully migrated. No usage of the deprecated `CommentManagerInterface::getCountNewComments()` exists anywhere in the test environment.

The setup script hardcoded `history` as the target, but `history` *provides* the new API — it wouldn't call the old one on `CommentManagerInterface`. A better test target would be a contrib module that still injects `CommentManagerInterface` and calls `->getCountNewComments()`. This deprecation landed in 11.3.0 so no such module may exist in the wild yet.

**Action:** Add a comment to the setup script explaining that no suitable contrib target was found for this rector.

---

#### M3: `ReplaceEditorLoadRector` — `ckeditor5_premium_features` (282 files), 0 changes

**Target pattern:** `editor_load()` function calls

**Suspicion:** `ckeditor5_premium_features` may use the entity storage API (`\Drupal::entityTypeManager()->getStorage('editor')->load(...)`) instead of the procedural `editor_load()`. That would be correct modern code and a true negative. Alternatively the setup script matched this module incorrectly.

**Investigation steps:**
1. Grep `ckeditor5_premium_features` for `editor_load`
2. If not found, verify the setup script's matching logic for this rector

**Status:** [ ] Investigated [ ] Fixed [ ] Test added

---

#### ~~M4: `ReplaceSessionManagerDeleteRector` — `role_expire` (15 files), 0 changes~~ FIXED

**Root cause:** The rector checked `ObjectType('Drupal\Core\Session\SessionManager')` (the concrete class). `role_expire` injects `SessionManagerInterface` and calls `->delete()` on it. PHPStan cannot resolve the subtype relationship without stubs, so the check failed.

**Fix:**
- Changed `ObjectType` to `SessionManagerInterface` in the rector
- Added PHPStan stubs for `SessionManagerInterface` and `SessionManager` (with `implements SessionManagerInterface`) so the type hierarchy resolves correctly for both concrete and interface-typed variables
- Converted `no_change_interface.php.inc` → `interface_variable.php.inc` (now a positive fixture)
- Added `interface_property.php.inc` for the injected class property pattern

**Status:** [x] Investigated [x] Fixed [x] Test added

---

#### M5: `StripMigrationDependenciesExpandArgRector` — `migrate_tools` (45 files), 0 changes

**Target pattern:** `$expand` argument in `MigrationPluginManager::createInstances()` or similar

**Suspicion:** Either the argument has already been removed in the installed version of `migrate_tools`, or the rector's argument-position check is off-by-one.

**Investigation steps:**
1. Grep `migrate_tools` for `createInstances`
2. Check the rector's expected method signature against what the module actually calls

**Status:** [ ] Investigated [ ] Fixed [ ] Test added

---

### P3 — Set Duplication (low priority)

#### D1: `ReplaceModuleHandlerGetNameRector` runs twice against `mailsystem`

**What happens:** The rector appears at log line ~722 and again at ~1297, scanning the same 16 files and producing identical diffs both times.

**Root cause:** The rector class is registered in two different rector sets (e.g. a Drupal 10 set and a generic/all set).

**Risk:** On a real (non-dry-run) run, the second pass would be a no-op since the first pass already applied the change. But it wastes time and is confusing.

**Fix needed:** Audit rector set definitions for duplicate class registrations. Remove the duplicate.

**Status:** [ ] Investigated [ ] Fixed

---

## Work Order

1. ~~**F1**~~ — `RemoveModuleHandlerAddModuleCallsRector` — CLOSED (rector correct, config_track has pre-existing bug)
2. ~~**F2**~~ — `ReplaceDateTimeRangeConstantsRector` — CLOSED (handles two patterns by design)
3. ~~**M2**~~ — `ReplaceCommentManagerGetCountNewCommentsRector` — CLOSED (true negative, history already migrated to new API)
4. ~~**M4**~~ — `ReplaceSessionManagerDeleteRector` — FIXED (now matches `SessionManagerInterface`-typed variables)
5. **M1** — `RemoveUpdaterPostInstallMethodsRector` interface resolution
6. **M3** — `ReplaceEditorLoadRector` true negative vs setup-script mismatch
7. **M5** — `StripMigrationDependenciesExpandArgRector` argument position
8. **D1** — Duplicate set registration for `ReplaceModuleHandlerGetNameRector`

---

## Out of Scope

- 20 rectors skipped due to missing modules in the test environment — expected gaps, not failures.
