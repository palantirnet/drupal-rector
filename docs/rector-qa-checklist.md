# Rector QA Checklist

**Next:** [`UseEntityTypeHasIntegerIdRector`](#useentitytypehasintergeridrector)


Living checklist for every rector added in the `main-bbrala` branch. Each rector gets three tasks: **Analyze**, **Coverage**, and **Edge cases**. Work through them iteratively — check a box when it is done.

---

## How to do each task

### Analyze

Goal: confirm the rector fully implements the deprecation described in the change record and matches the intended behaviour in the drupal-digest source.

Steps:
1. Read the rector source at the `src/` path listed under the rector.
2. Open the drupal-digest source file listed (in the local `drupal-digests` repo at `rector/rules/<file>`).
3. Read the actual deprecated and replacement code from the local Drupal core clone at `~/projects/drupal-core` (11.x). Prefer this over fetching drupal.org URLs — the source is authoritative and faster.
4. Fetch the change record URL if additional context is needed (e.g. the CR lists multiple deprecated items not obvious from the source).
5. Answer these questions:
   - Are all deprecated items (methods, constants, properties, functions) from the change record handled by the rector?
   - Does the drupal-digest version handle anything the drupal-rector version does not (extra guards, additional node types, `self::`/`static::` variants, etc.)?
   - Does the change record describe a read *and* a write side (e.g. getter + setter), and does the rector handle both?
   - Is the deprecation warning documented correctly in the rector's docblock (`@see` URL, version, removal version)?
5. Write down the gaps found as notes under the task checkbox.

### Coverage

Goal: ensure the test fixture exercises every transformation that the change record describes.

Steps:
1. Read the existing fixture at `tests/src/.../fixture/basic.php.inc`.
2. Compare it against the before/after examples in the change record.
3. For each transformation variant in the change record that is missing from the fixture, add it.
4. Add fixture entries as new before/after pairs separated by `-----` if the test runner supports multiple pairs, or create a second fixture file (e.g. `extended.php.inc`) and wire it up in the test class.
5. Run `./vendor/bin/phpunit tests/src/.../<RectorName>RectorTest.php` to confirm all pass.

### Edge cases

Goal: harden the rector against inputs it should transform but currently does not, and inputs it should *not* transform but might accidentally change.

Common edge cases to check for every rector:
- **`self::`/`static::`/`parent::` class constant references** — does the rector handle them when used inside a subclass?
- **Aliased imports** — `use Foo\Bar as Baz; Baz::CONST` — does the rector still match?
- **Receiver typed as a concrete class instead of an interface** — e.g. `NodeStorage` vs `NodeStorageInterface`.
- **Return value used in a fluent chain** — does the replacement expression fit correctly?
- **Return value unused / used as a statement** — the rector should handle both.
- **Named arguments** — does the rector skip or handle `func(arg: $val)`?
- **Multiple calls on the same line/expression** — no double-transform.
- **Nodes the rector should NOT touch** — unrelated methods/functions/classes with the same name but different type/context.

For each edge case found, add a fixture pair and confirm the test still passes.

### Finishing a rector

When all three tasks for a rector are checked off, update the `**Next:**` line at the very top of this file to point to the next rector that still has unchecked tasks. Use the rector's section heading as the anchor (lowercase, spaces replaced with hyphens, no special characters).

---

## Drupal 10 Rectors

### ReplaceModuleHandlerGetNameRector
- Source: `src/Drupal10/Rector/Deprecation/ReplaceModuleHandlerGetNameRector.php`
- Test: `tests/src/Drupal10/Rector/Deprecation/ReplaceModuleHandlerGetNameRector/`
- Drupal-digest: `replace-removed-modulehandlerinterface-getname-with-3571063.php`
- Change record: https://www.drupal.org/node/3310017 (the `@see` in the rector points to the issue node/3571063; the actual change record is node/3310017)

Tasks:
- [x] **Analyze** — gaps found:
  - `@see` in rector docblock points to the issue (node/3571063), not the change record (node/3310017) — minor, both are valid references
  - Drupal-digest does a direct replacement; our rector wraps in `DeprecationHelper::backwardsCompatibleCall` via `AbstractDrupalCoreRector` — correct and intentional
  - Only one deprecated method (`getName`) — full API coverage matches the change record; no other items missing
  - Existing fixture only covered annotated local variable form; class property form was missing
- [x] **Coverage** — added `fixture/class_property.php.inc`: `$this->moduleHandler->getName($module)` in a class with typed constructor property → BC-wrapped output; all 3 tests pass
- [x] **Edge cases** — added `fixture/no_change_unrelated.php.inc`: `getName()` on an `UnrelatedManager` typed variable → correctly not transformed; confirmed by test pass

---

### ReplaceRebuildThemeDataRector
- Source: `src/Drupal10/Rector/Deprecation/ReplaceRebuildThemeDataRector.php`
- Test: `tests/src/Drupal10/Rector/Deprecation/ReplaceRebuildThemeDataRector/`
- Drupal-digest: `replace-removed-themehandlerinterface-rebuildthemedata-with-3571068.php`
- Change record: https://www.drupal.org/node/3413196 (the `@see` in the rector points to the issue node/3571068; the actual change record is node/3413196)

Tasks:
- [x] **Analyze** — gaps found:
  - `@see` in rector docblock points to the issue (node/3571068), not the change record (node/3413196)
  - **No type guard** — neither our rector nor the drupal-digest version checks `isObjectType(ThemeHandlerInterface)`; any `rebuildThemeData()` call with no args on any object is transformed. Low collision risk given the specific method name, but worth noting.
  - Drupal-digest does direct replacement; our rector uses BC wrapping via `AbstractDrupalCoreRector` — correct and intentional
  - Only one deprecated method — full API coverage matches the change record
- [x] **Coverage** — added `fixture/class_property.php.inc`: `$this->themeHandler->rebuildThemeData()` in a class with typed constructor property → BC-wrapped output; all 3 tests pass
- [x] **Edge cases** — added `fixture/no_change_with_args.php.inc`: `rebuildThemeData($extra)` with an argument → correctly not transformed by the `!empty($node->args)` guard. Note: no test for unrelated class because there is no type guard — such a call *would* be (incorrectly) transformed; documented above as a known gap.

---

### ReplaceRequestTimeConstantRector
- Source: `src/Drupal10/Rector/Deprecation/ReplaceRequestTimeConstantRector.php`
- Test: `tests/src/Drupal10/Rector/Deprecation/ReplaceRequestTimeConstantRector/`
- Drupal-digest: `replace-deprecated-request-time-constant-with-drupal-time-3395986.php`
- Change record: https://www.drupal.org/node/3395986

Tasks:
- [x] **Analyze** — gaps found:
  - Rector and drupal-digest are identical in logic — both target `ConstFetch` nodes named `REQUEST_TIME` and replace with `\Drupal::time()->getRequestTime()`
  - The `@see` URL (node/3395986) is the same reference used in the drupal-digest source — consistent
  - Docblock version ("deprecated drupal:8.3.0, removed drupal:11.0.0") is correct
  - No gaps: only one deprecated item (`REQUEST_TIME`), fully handled
- [x] **Coverage** — added `fixture/in_function_call.php.inc`: `REQUEST_TIME` as function argument, array value, and in string concatenation — all transformed; 3 tests pass
- [x] **Edge cases** — added `fixture/no_change_string_literal.php.inc`: string literals `'REQUEST_TIME'` and `"REQUEST_TIME"` (String_ nodes, not ConstFetch) — correctly not transformed; confirmed by test pass

---

## Drupal 11 Rectors

### ErrorCurrentErrorHandlerRector
- Source: `src/Drupal11/Rector/Deprecation/ErrorCurrentErrorHandlerRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ErrorCurrentErrorHandlerRector/`
- Drupal-digest: `replace-error-currenterrorhandler-with-get-error-handler-3526515.php`
- Change record: https://www.drupal.org/node/3526515

Tasks:
- [x] **Analyze** — gaps found:
  - `@see` in rector and digest reference `node/3526515` (the issue); the actual deprecation notice in `core/lib/Drupal/Core/Utility/Error.php:221` says `node/3529500` — minor discrepancy, both point to the same change
  - Rector and digest are identical in logic; one deprecated item (`currentErrorHandler`), fully handled
  - `isObjectType(ObjectType('Drupal\Core\Utility\Error'))` type guard correctly excludes unrelated classes
  - Basic fixture already covers: FQCN form, result-assigned-to-variable, `OtherClass::` negative case
- [x] **Coverage** — basic fixture already covered all change-record variants (single method, single replacement); no additions needed
- [x] **Edge cases** — added `fixture/as_argument.php.inc`: result used as function argument and in boolean expression (via `use` import short-name form); both transformed correctly; 2 tests pass

---

### FileSystemBasenameToNativeRector
- Source: `src/Drupal11/Rector/Deprecation/FileSystemBasenameToNativeRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/FileSystemBasenameToNativeRector/`
- Drupal-digest: `replace-filesysteminterface-basename-with-native-basename-3530461.php`
- Change record: https://www.drupal.org/node/3530461

Tasks:
- [x] **Analyze** — gaps found:
  - `@see` in rector and digest reference `node/3530461` (the issue); actual deprecation in `FileSystemInterface.php` and `FileSystem.php` says `node/3530869` — same discrepancy pattern as previous rectors
  - Our rector uses Rector's `isObjectType()` helper (catches any implementor of `FileSystemInterface`); digest uses `isSuperTypeOf()` (exact type match only — our rector is stronger)
  - Both deprecated locations handled: `FileSystemInterface::basename()` and `FileSystem::basename()` (via the two-class loop)
  - No other deprecated items in this change record
- [x] **Coverage** — added `fixture/no_suffix.php.inc`: one-argument call (no `$suffix`) → `basename($uri)`; 3 tests pass
- [x] **Edge cases** — added `fixture/concrete_class.php.inc`: receiver typed as concrete `\Drupal\Core\File\FileSystem` → transformed; unrelated `$untyped->basename()` already covered as no-change in `basic.php.inc`; 3 tests pass

---

### LoadAllIncludesRector
- Source: `src/Drupal11/Rector/Deprecation/LoadAllIncludesRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/LoadAllIncludesRector/`
- Drupal-digest: `replace-deprecated-modulehandler-loadallincludes-with-3536431.php`
- Change record: https://www.drupal.org/node/3536431

Tasks:
- [x] **Analyze** — gaps found:
  - `@see` references `node/3536431` (issue); core deprecation notice says `node/3536432` (CR) — same discrepancy pattern
  - **No type guard** — neither rector nor digest checks `isObjectType(ModuleHandlerInterface)`; any `loadAllIncludes()` call on any receiver is transformed (same known gap as `ReplaceRebuildThemeDataRector`)
  - Rector and digest are otherwise identical in logic; both variants (one-arg, two-arg) handled correctly
- [x] **Coverage** — basic fixture already covers one-arg and two-arg forms with `$this->moduleHandler`; added `fixture/drupal_module_handler.php.inc` for `\Drupal::moduleHandler()` chained receiver
- [x] **Edge cases** — `\Drupal::moduleHandler()` chained receiver works correctly (cloned StaticCall used in both inner calls); no no-change fixture possible — no type guard means any `loadAllIncludes()` is transformed; documented as known gap above; 2 tests pass

---

### MigrateSqlGetMigrationPluginManagerRector
- Source: `src/Drupal11/Rector/Deprecation/MigrateSqlGetMigrationPluginManagerRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/MigrateSqlGetMigrationPluginManagerRector/`
- Drupal-digest: `replace-deprecated-sql-getmigrationpluginmanager-with-3439369.php`
- Change record: https://www.drupal.org/node/3439369

Tasks:
- [x] **Analyze** — gaps found:
  - `@see` in rector: `node/3439369`; digest references `node/3277306` — different nodes; node/3439369 is the change record used in the rector
  - Method removed from `Sql.php` in drupal:11.0.0 — confirmed absent from 11.x core
  - Rector restricts to `$this->...` only (protected method pattern) — `$other->getMigrationPluginManager()` correctly not matched
  - Static `Migration::getMigrationPluginManager()` is a `StaticCall` node; rector only handles `MethodCall` — naturally excluded
  - `isObjectType(ObjectType('Drupal\migrate\Plugin\Migration'))` exclusion guard correct but **untestable in this repo** — `drupal/migrate` not in vendor
- [x] **Coverage** — `$this` form already in basic fixture; added `fixture/chain_result.php.inc`: result used in method chain (`->createInstances()`); 2 tests pass
- [x] **Edge cases** — `$other->getMigrationPluginManager()` not matched (var !== `$this`); static call naturally excluded (StaticCall node); Migration subclass exclusion guard correct but not fixture-testable (drupal/migrate not in vendor — documented above)

---

### NodeStorageDeprecatedMethodsRector
- Source: `src/Drupal11/Rector/Deprecation/NodeStorageDeprecatedMethodsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/NodeStorageDeprecatedMethodsRector/`
- Drupal-digest: `replace-deprecated-nodestorage-revisionids-and-3396062.php`
- Change record: https://www.drupal.org/node/3519187

Tasks:
- [x] **Analyze** — gaps found:
  - `@see` in rector: `node/3396062` (issue); actual change record is `node/3519187` — confirmed in `NodeStorage.php` deprecation notices
  - All three deprecated methods confirmed in `NodeStorageInterface`: `revisionIds`, `userRevisionIds`, `countDefaultLanguageRevisions` (all deprecated in drupal:11.3.0, removed in drupal:13.0.0)
  - **Gap fixed**: `countDefaultLanguageRevisions` (no replacement) was not handled — added statement-level removal using `NodeVisitor::REMOVE_NODE`
- [x] **Coverage** — added `fixture/count_default_language_revisions.php.inc`: statement removed, surrounding code preserved; added `fixture/foreach_usage.php.inc`: `revisionIds()` result used in `foreach` loop; 3 tests pass
- [x] **Edge cases** — argument as method call (`$this->getNode()`) handled correctly by `$node->args[0]` extraction; both methods in same block covered by basic fixture; receiver typed as concrete `NodeStorage` not fixture-testable (drupal/node not in vendor) but type guard is interface-based so any `NodeStorageInterface` implementor matches

---

### PluginBaseIsConfigurableRector
- Source: `src/Drupal11/Rector/Deprecation/PluginBaseIsConfigurableRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/PluginBaseIsConfigurableRector/`
- Drupal-digest: `replace-deprecated-pluginbase-isconfigurable-with-3459533.php`
- Change record: https://www.drupal.org/node/3459533

Tasks:
- [x] **Analyze** — gaps found:
  - `@see` in rector: `node/3459533`; digest references `node/3198285` — different nodes
  - `isConfigurable()` absent from `PluginBase.php` in 11.x — confirmed removed
  - Rector intentionally restricts to `$this->isConfigurable()` only — avoids false positives on `CKEditor5PluginDefinition`, `Action`, etc. which have own `isConfigurable()` with different semantics (documented in digest)
  - No other deprecated items in this change record
- [x] **Coverage** — basic fixture covers if-condition; added `fixture/negated.php.inc`: `!$this->isConfigurable()` and `return $this->isConfigurable()` — both correctly transformed; 3 tests pass
- [x] **Edge cases** — added `fixture/no_change_other_variable.php.inc`: `$plugin->isConfigurable()` on non-`$this` var → correctly not transformed; `$this->...` within any class body is transformed (no type guard by design — documented above); 3 tests pass

---

### RemoveAutomatedCronSubmitHandlerRector
- Source: `src/Drupal11/Rector/Deprecation/RemoveAutomatedCronSubmitHandlerRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RemoveAutomatedCronSubmitHandlerRector/`
- Drupal-digest: `remove-deprecated-automated-cron-settings-submit-calls-and-3566768.php`
- Change record: https://www.drupal.org/node/3566768

Tasks:
- [x] **Analyze** — gaps found:
  - `@see` in rector: `node/3566768` (issue); actual deprecation in `automated_cron.module` says `node/3566774` (CR) — same discrepancy pattern
  - Digest handles the `$form['#submit'][]` assignment only; direct function call `automated_cron_settings_submit(...)` is handled via `FunctionCallRemovalRector` config entry in `config/drupal-11/drupal-11.4-deprecations.php` — both removal paths are covered
  - Rector and digest logic are identical for the assignment case
- [x] **Coverage** — basic fixture already covers: array-append removal and different-value no-change; function call removal tested via `FunctionCallRemovalRector` (separate rector/test)
- [x] **Edge cases** — added `fixture/no_change_explicit_index.php.inc`: `$form['#submit'][0] = 'automated_cron_settings_submit'` (explicit index → `dim !== null` guard correctly prevents removal); 2 tests pass

---

### RemoveCacheExpireOverrideRector
- Source: `src/Drupal11/Rector/Deprecation/RemoveCacheExpireOverrideRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RemoveCacheExpireOverrideRector/`
- Drupal-digest: `remove-deprecated-cacheexpire-overrides-from-views-3576556.php`
- Change record: https://www.drupal.org/node/3576556

Tasks:
- [x] **Analyze** — gaps found:
  - Rector and drupal-digest are functionally identical in logic (both remove `cacheExpire()` from `CachePluginBase` subclasses)
  - Drupal-digest uses `$objectType->isSuperTypeOf($extendsType)->yes()` for the PHPStan fallback; rector uses `$this->isObjectType($node->extends, ...)` — different APIs but same intent
  - `@see` URL (`node/3576556`), deprecation version (`drupal:11.4.0`), and removal version (`drupal:13.0.0`) are all correct
  - `PARENT_SHORT_NAMES` includes all four known short-name subclasses: `CachePluginBase`, `Time`, `Tag`, `None` — full coverage
  - Only one deprecated item (`cacheExpire()`) — no gaps in change record coverage
- [x] **Coverage** — added:
  - `fixture/extends_time.php.inc`: class extending `Time` short name → `cacheExpire()` removed, `cacheSetMaxAge()` preserved
  - `fixture/extends_tag.php.inc`: class extending `Tag` short name → `cacheExpire()` removed, class with empty body
  - `fixture/extends_none.php.inc`: class extending `None` short name → `cacheExpire()` removed, class with empty body
  - `fixture/no_change_unrelated.php.inc`: class with no `extends` → `cacheExpire()` correctly NOT removed
  - All 8 tests pass
- [x] **Edge cases** — added:
  - `fixture/side_effects_body.php.inc`: `cacheExpire()` body with logging + property side effects → still removed entirely (correct; caller must migrate side effects manually)
  - `fixture/extends_fqcn.php.inc`: extends `\Drupal\views\Plugin\views\cache\CachePluginBase` by FQCN → matched by `CACHE_PLUGIN_BASE_FQCN` constant, removed correctly
  - `fixture/namespace_alias.php.inc`: `use CachePluginBase as ViewsCache; class Foo extends ViewsCache` → resolved by PHPStan fallback, removed correctly
  - All 8 tests pass

---

### RemoveConfigSaveTrustedDataArgRector
- Source: `src/Drupal11/Rector/Deprecation/RemoveConfigSaveTrustedDataArgRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RemoveConfigSaveTrustedDataArgRector/`
- Drupal-digest: `remove-deprecated-trustdata-calls-and-save-true-argument-in-3347842.php`
- Change record: https://www.drupal.org/node/3347842

Tasks:
- [x] **Analyze** — gaps found:
  - Both sides of the deprecation are covered: `RemoveTrustDataCallRector` handles `trustData()` method call removal; this rector handles the `save(TRUE)` / `save(FALSE)` boolean arg removal — no overlap or double-application risk
  - `@see` in rector docblock points to `node/3347842` (the digest issue); Drupal core's actual deprecation notice in `Config::save()` and `ConfigEntityBase::trustData()` both reference `node/3348180` — minor discrepancy, same change
  - Deprecation version (`drupal:11.4.0`) and removal version (`drupal:13.0.0`) match core exactly
  - **No type guard** — the rector removes the arg from any `save(boolean)` call on any receiver, not just `Config` objects. Intentional by design (same pattern as `ReplaceRebuildThemeDataRector`): `save()` with a boolean literal is highly specific to this deprecated pattern; false-positive risk is low and matches the digest approach
  - `save(FALSE)` is handled correctly (rector checks both `'true'` and `'false'` in strtolower comparison)
  - `hasTrustedData()` intentionally NOT deprecated yet — deferred to Drupal 13; no rector needed
- [x] **Coverage** — `basic.php.inc` already covers all required variants: `save(TRUE)` → `save()`; `save(FALSE)` → `save()`; `save()` no-arg unchanged; `$other->save(TRUE)` transformed (demonstrating no type guard); 2 tests pass
- [x] **Edge cases** — added `fixture/no_change_variable_arg.php.inc`: `$config->save($trusted)` where arg is a variable (not a boolean literal) — correctly not transformed (ConstFetch guard prevents it); 2 tests pass

---

### RemoveHandlerBaseDefineExtraOptionsRector
- Source: `src/Drupal11/Rector/Deprecation/RemoveHandlerBaseDefineExtraOptionsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RemoveHandlerBaseDefineExtraOptionsRector/`
- Drupal-digest: `remove-overrides-of-deprecated-handlerbase-3485084.php`
- Change record: https://www.drupal.org/node/3485084

Tasks:
- [x] **Analyze** — gaps found:
  - Rector's `@see` URL (node/3485084) matches the drupal-digest source comment; digest `@see` says node/3486781 (different node) — rector is consistent with the digest file header
  - Digest does NOT check `extends` at all — it removes `defineExtraOptions()` from any class that is not named `HandlerBase` itself; our rector is stronger and more correct: it only removes the method from known subclass hierarchies
  - Rector correctly handles all four sub-plugin bases: `FilterPluginBase`, `SortPluginBase`, `ArgumentPluginBase`, `RelationshipPluginBase` — all in `PARENT_SHORT_NAMES`
  - `FieldHandlerBase` is also in `PARENT_SHORT_NAMES`; not in the digest — a bonus improvement
  - FQCN check (`\Drupal\views\Plugin\views\HandlerBase`) handled via direct `toString()` comparison
  - Backslash-prefixed short name handled via `str_ends_with($parentName, '\\'.$short)` — correct
  - PHPStan `ObjectType` fallback for when types are resolvable — belt-and-suspenders approach
  - Versions correct: deprecated in drupal:11.2.0, removed in drupal:12.0.0
- [x] **Coverage** — added `fixture/filter_plugin_base.php.inc` (with extra method preserved), `fixture/sort_plugin_base.php.inc`, `fixture/argument_plugin_base.php.inc`, `fixture/relationship_plugin_base.php.inc`; all 8 tests pass
- [x] **Edge cases** — added `fixture/fqcn_extends.php.inc` (FQCN `\Drupal\views\Plugin\views\HandlerBase`), `fixture/non_empty_body.php.inc` (multi-line body still removed), `fixture/no_change_unrelated_class.php.inc` (class with no `extends` → not touched); all 8 tests pass

---

### RemoveLinkWidgetValidateTitleElementRector
- Source: `src/Drupal11/Rector/Deprecation/RemoveLinkWidgetValidateTitleElementRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RemoveLinkWidgetValidateTitleElementRector/`
- Drupal-digest: `remove-deprecated-linkwidget-validatetitleelement-calls-3093118.php`
- Change record: https://www.drupal.org/node/3093118

Tasks:
- [x] **Analyze** — rector is a static-call removal rector; only one deprecated item in the change record (`LinkWidget::validateTitleElement()`), fully handled; both digest and rector check for static call on the exact FQCN `Drupal\link\Plugin\Field\FieldWidget\LinkWidget` — no type inference needed since the class is hardcoded in the guard; rector's `@see` URL uses node/3093118 (the digest's change-record reference) but Drupal core's `@deprecated` tag links to node/3554139 (a different issue) — this is a minor discrepancy but consistent with the digest source; no instance-method-call variant exists
- [x] **Coverage** — existing `basic.php.inc` covers the main case (aliased `use` import + static call removal); added `fqcn.php.inc` for FQCN static call without `use` statement
- [x] **Edge cases** — added `no_change_unrelated_class.php.inc` (static call on `SomeOtherWidget` is not removed); added `no_change_method_call.php.inc` (instance method call `$linkWidget->validateTitleElement()` is not removed because rector only matches `StaticCall` nodes); all 4 tests pass

---

### RemoveModuleHandlerAddModuleCallsRector
- Source: `src/Drupal11/Rector/Deprecation/RemoveModuleHandlerAddModuleCallsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RemoveModuleHandlerAddModuleCallsRector/`
- Drupal-digest: `remove-deprecated-modulehandlerinterface-addmodule-and-3528899.php`
- Change record: https://www.drupal.org/node/3528899

Tasks:
- [x] **Analyze** — gaps found:
  - Both `addModule()` and `addProfile()` are handled — confirmed in rector and basic fixture
  - `@see` in rector: `node/3528899` (issue); Drupal core `ModuleHandlerInterface` deprecation uses `node/3491200` (CR) — minor discrepancy, same change
  - Versions correct: deprecated in drupal:11.2.0, removed in drupal:12.0.0
  - Rector uses `isObjectType(ModuleHandlerInterface)` — concrete `ModuleHandler` class not matched unless PHPStan knows its hierarchy; **fixed**: updated rector to also explicitly check `ModuleHandler` concrete class, matching `FileSystemBasenameToNativeRector` pattern
  - No type guard gap risk: method names `addModule`/`addProfile` are specific enough
- [x] **Coverage** — `addProfile()` already in `basic.php.inc`; added `fixture/concrete_class.php.inc`: receiver typed as `\Drupal\Core\Extension\ModuleHandler` → both calls removed; 4 tests pass
- [x] **Edge cases** — added `fixture/no_change_unrelated_class.php.inc`: `$manager->addModule()` on `SomeOtherManager` → not removed; added `fixture/no_change_fluent_chain.php.inc`: `$mh->addModule(...)->getSomething()` — chain form not removable as statement (outer method name ≠ addModule) → not touched; 4 tests pass

---

### RemoveModuleHandlerDeprecatedMethodsRector
- Source: `src/Drupal11/Rector/Deprecation/RemoveModuleHandlerDeprecatedMethodsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RemoveModuleHandlerDeprecatedMethodsRector/`
- Drupal-digest: `remove-deprecated-modulehandlerinterface-writecache-and-3442009.php`
- Change record: https://www.drupal.org/node/3442009

Tasks:
- [x] **Analyze** — gaps found:
  - Both transformations confirmed: `writeCache()` removed as statement (`NodeVisitor::REMOVE_NODE`); `getHookInfo()` replaced with `[]` when used as expression, also removed when used as standalone statement
  - Rector has a correct `isObjectType(ModuleHandlerInterface)` type guard — untyped receivers are NOT transformed
  - `@see` URL in rector docblock is `node/3442009` (matches drupal-digest); Drupal core's deprecation notices in `ModuleHandlerInterface.php` say `node/3442349` — minor discrepancy, same change
  - Versions correct: deprecated in drupal:11.1.0, removed in drupal:12.0.0
  - Rector and digest are functionally identical — no missing transformations
- [x] **Coverage** — added `fixture/get_hook_info_expressions.php.inc`: `getHookInfo()` used as function argument (`doSomething([])`) and as `foreach` iterable — both transformed correctly; 4 tests pass
- [x] **Edge cases** — added `fixture/no_change_unrelated_class.php.inc`: `$untyped->getHookInfo()` and `$untyped->writeCache()` on untyped receivers — correctly NOT transformed (type guard works); added `fixture/write_cache_chained_receiver.php.inc`: `$builder->getModuleHandler()->writeCache()` — PHPStan cannot resolve chained return type so it is also NOT transformed (documented as known limitation); 4 tests pass

---

### RemoveRootFromConvertDbUrlRector
- Source: `src/Drupal11/Rector/Deprecation/RemoveRootFromConvertDbUrlRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RemoveRootFromConvertDbUrlRector/`
- Drupal-digest: `remove-deprecated-string-root-from-database-3522513.php`
- Change record: https://www.drupal.org/node/3522513

Tasks:
- [x] **Analyze** — rector and drupal-digest are logically identical; heuristic is correct: ConstFetch(true/false/null) and Variable are skipped; String_, PropertyFetch, NullsafePropertyFetch, FuncCall, StaticPropertyFetch, MethodCall are treated as string root and removed. Minor note: rector `@see` uses `node/3522513` (change record) while core's own deprecation message cites `node/3511287` (issue) — both refer to the same change; the change record reference is the better one. Removal version in docblock is `12.0.0` which matches core. No gaps.
- [x] **Coverage** — added `fixture/two_args_string.php.inc` (string literal root removed), `fixture/two_args_property_fetch.php.inc` (property fetch only, no third arg), `fixture/three_args_property_fetch.php.inc` (three-arg shifts bool), `fixture/method_call_second_arg.php.inc` (method call result removed, three-arg variant shifts bool); 7 tests pass
- [x] **Edge cases** — added `fixture/no_change_bool_second_arg.php.inc` (TRUE/FALSE → unchanged) and `fixture/no_change_variable_second_arg.php.inc` ($root variable → unchanged); all 7 tests pass

---

### RemoveSetUriCallbackRector
- Source: `src/Drupal11/Rector/Deprecation/RemoveSetUriCallbackRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RemoveSetUriCallbackRector/`
- Drupal-digest: `remove-deprecated-entitytypeinterface-seturicallback-calls-2667040.php`
- Change record: https://www.drupal.org/node/2667040

Tasks:
- [x] **Analyze** — both cases confirmed: standalone `Expression` removal via `NodeVisitor::REMOVE_NODE` and mid-chain `MethodCall` removal by replacing `$node->var` with `$node->var->var`; `@see` URL (node/2667040) and versions (deprecated drupal:11.4.0, removed drupal:13.0.0) are correct; note: `getUriCallback()` is also deprecated in the same CR but is a read-side accessor not in scope for this removal rector; no type guard (acceptable — method name is unique to `EntityTypeInterface` in Drupal core)
- [x] **Coverage** — `basic.php.inc` already covered standalone removal and single-level mid-chain; added `fixture/deep_chain.php.inc`: `$et->setLinkTemplate(...)->setUriCallback(...)->setLabel(...)` — deeply nested chain where `setUriCallback()` has a MethodCall receiver; all 4 tests pass
- [x] **Edge cases** — added `fixture/no_type_guard.php.inc`: demonstrates no type guard — `$unrelated_object->setUriCallback()` is also removed; acceptable because the method name is unique in Drupal core; added `fixture/no_change_assignment.php.inc`: `$x = $et->setUriCallback(...)` (assignment wrapper) — correctly NOT transformed because the `Expression` check requires `$node->expr` to be a `MethodCall` directly, and the MethodCall check only handles chained receivers; all 4 tests pass

---

### RemoveStateCacheSettingRector
- Source: `src/Drupal11/Rector/Deprecation/RemoveStateCacheSettingRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RemoveStateCacheSettingRector/`
- Drupal-digest: `remove-deprecated-settings-state-cache-assignment-for-3436954.php`
- Change record: https://www.drupal.org/node/3436954

Tasks:
- [x] **Analyze** — gaps found:
  - Rector and digest are functionally identical — both remove `$settings['state_cache']` assignments by returning `NodeVisitor::REMOVE_NODE` from an `Expression` visitor
  - `@see` in rector uses `node/3436954` (the digest issue); Drupal core's `Settings.php` deprecation message references `node/3177901` — minor discrepancy, both point to the same change
  - Deprecation version `drupal:11.0.0` confirmed correct in `core/lib/Drupal/Core/Site/Settings.php`; no removal version specified (setting is simply gone, not replaced)
  - Only one deprecated item (`$settings['state_cache']`), fully handled; basic fixture covers `TRUE` value and unrelated key preserved
  - No type guard needed — the guard is structural: variable name `$settings` + key string `'state_cache'`
- [x] **Coverage** — added `fixture/false_value.php.inc`: `$settings['state_cache'] = FALSE` also removed (basic only covered `TRUE`); 4 tests pass
- [x] **Edge cases** — added `fixture/no_change_similar_key.php.inc`: `$settings['state_cache_bin']`, `$settings['disable_state_cache']`, `$settings['cache']` — none removed; added `fixture/no_change_nested_array.php.inc`: `$config['system']['state_cache']` and `['state_cache' => TRUE]` — not matched because outer variable is not `$settings`; 4 tests pass

---

### RemoveTrustDataCallRector
- Source: `src/Drupal11/Rector/Deprecation/RemoveTrustDataCallRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RemoveTrustDataCallRector/`
- Drupal-digest: `remove-deprecated-trustdata-calls-and-save-true-argument-in-3347842.php`
- Change record: https://www.drupal.org/node/3347842

Tasks:
- [x] **Analyze** — confirmed: this rector handles only `trustData()` method call removal; `RemoveConfigSaveTrustedDataArgRector` handles `save(TRUE/FALSE)` — no overlap. `@see` points to node/3347842 (the broader change record used in the digest); Drupal core's `@trigger_error` references node/3348180 (the specific deprecation node) — same discrepancy as the sibling rector, intentional. No type guard: `trustData()` is unique enough that false positives are not a concern in practice.
- [x] **Coverage** — added `standalone_statement.php.inc` (trustData() as bare statement → `$entity;`), `assigned_result.php.inc` (result assigned to variable). Chained usage was already in `basic.php.inc`.
- [x] **Edge cases** — added `no_change_other_method.php.inc` (getData, save, trustMe — none changed). No type guard in the rector: trustData() on any class is removed; this is consistent with the sibling rector's design and safe given the method name's uniqueness. 4/4 tests pass.

---

### RemoveTwigNodeTransTagArgumentRector
- Source: `src/Drupal11/Rector/Deprecation/RemoveTwigNodeTransTagArgumentRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RemoveTwigNodeTransTagArgumentRector/`
- Drupal-digest: `remove-deprecated-tag-argument-from-twignodetrans-3473440.php`
- Change record: https://www.drupal.org/node/3473440

Tasks:
- [x] **Analyze** — gaps found:
  - `@see` points to node/3473440 (the meta-issue); the actual constructor change landed in child issue #3477374 — the digest also references node/3473440, so this is consistent and intentional
  - Rector and drupal-digest are functionally identical in logic: only the 6-argument form is changed, 6th arg (`$tag`) is removed via `array_pop`
  - Rector explicitly checks both `TwigNodeTrans` (short name after `use` import) and `Drupal\Core\Template\TwigNodeTrans` (FQCN without leading backslash); Rector's name resolver handles aliased imports at the framework level so alias resolution works automatically
  - No version/removal annotations in the docblock — not required given the pattern used across other rectors; no other deprecated items in the change record
- [x] **Coverage** — added `fixture/fqcn.php.inc`: FQCN form `new \Drupal\Core\Template\TwigNodeTrans(6 args)` → 5 args; added `fixture/no_change_fewer_args.php.inc`: 5-arg and 4-arg forms → correctly unchanged; all 4 tests pass
- [x] **Edge cases** — added `fixture/aliased_import.php.inc`: `use TwigNodeTrans as NodeTrans; new NodeTrans(6 args)` → Rector's name resolver correctly resolves the alias to the FQCN, 6th arg removed; `no_change_fewer_args.php.inc` confirms the `count($node->args) !== 6` guard works for both 5-arg and 4-arg forms; all 4 tests pass

---

### RemoveUpdaterPostInstallMethodsRector
- Source: `src/Drupal11/Rector/Deprecation/RemoveUpdaterPostInstallMethodsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RemoveUpdaterPostInstallMethodsRector/`
- Drupal-digest: `remove-deprecated-updater-postinstall-postinstalltasks-3417136.php`
- Change record: https://www.drupal.org/node/3417136

Tasks:
- [x] **Analyze** — gaps found:
  - `@see` in rector docblock points to `node/3417136` (the digest issue/change record); Drupal core's actual deprecation notice in `Updater.php` for `postInstall`/`postInstallTasks` says `node/3461934` — minor discrepancy, both are valid references
  - Deprecation version (`drupal:11.1.0`) and removal version (`drupal:12.0.0`) are correct per core source
  - Both `postInstall()` and `postInstallTasks()` are handled — correct
  - `Drupal\Core\Updater\Module` and `Drupal\Core\Updater\Theme` are both in `UPDATER_BASE_CLASSES` — correct
  - **Known gap**: short-name `extends Updater` (without `use` import resolved to FQCN) is NOT matched — `$node->extends->toString()` returns `Updater` which is not in the FQCN list; documented as known limitation (same pattern as `RemoveCacheExpireOverrideRector` short-name gap)
  - Rector and drupal-digest are functionally identical in logic
- [x] **Coverage** — added:
  - `fixture/extends_module.php.inc`: class extending `Drupal\Core\Updater\Module` → `postInstall()` removed, other method preserved
  - `fixture/extends_theme.php.inc`: class extending `Drupal\Core\Updater\Theme` → `postInstallTasks()` removed, other method preserved
  - `fixture/non_empty_body.php.inc`: both methods with multi-line bodies → both removed entirely, class left empty
  - All 5 tests pass
- [x] **Edge cases** — added:
  - `fixture/no_change_short_name.php.inc`: `extends Updater` without FQCN import → correctly NOT transformed (short-name not in FQCN list; documented as known limitation)
  - Unrelated class (`UnrelatedClass` with `postInstall()`) already covered in `basic.php.inc` — correctly not modified
  - All 5 tests pass

---

### RemoveViewsRowCacheKeysRector
- Source: `src/Drupal11/Rector/Deprecation/RemoveViewsRowCacheKeysRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RemoveViewsRowCacheKeysRector/`
- Drupal-digest: `remove-deprecated-cachepluginbase-getrowcachekeys-and-3564937.php`
- Change record: https://www.drupal.org/node/3564937

Tasks:
- [x] **Analyze** — gaps found:
  - `@see` in rector docblock pointed to `node/3564937` (the digest issue number); the actual Drupal core deprecation notice in `CachePluginBase.php` references `node/3564958` — fixed to use the correct change record URL
  - Deprecation version (`drupal:11.4.0`) and removal version (`drupal:13.0.0`) are correct per core source
  - Both `getRowCacheKeys()` and `getRowId()` are in `DEPRECATED_METHODS` — correct, both are deprecated in core
  - Rector logic is functionally identical to the drupal-digest; no extra type guard in either (name-based matching only)
  - The rector only removes array items where the value is a deprecated method call — standalone calls outside arrays are NOT affected (correct: nothing to remove from)
- [x] **Coverage** — added:
  - `fixture/get_row_id.php.inc`: `getRowId()` used as array item value is removed, non-deprecated items preserved
  - `fixture/both_deprecated_calls.php.inc`: both `getRowCacheKeys()` and `getRowId()` in the same array — both removed in one pass
- [x] **Edge cases** — verified:
  - `fixture/no_change_standalone_call.php.inc`: bare `$cache_plugin->getRowCacheKeys($row)` and `getRowId()` calls outside an array are not modified — rector targets `Array_` nodes only
  - `fixture/no_change_unrelated_class.php.inc`: a class with `getRowCacheKeys()` and `getRowId()` method **definitions** (not calls) is not touched — method declarations are not `Array_` nodes
  - Rector is name-based (no type guard); intentional since these method names are unique to Drupal Views `CachePluginBase`

---

### RenameStopProceduralHookScanRector
- Source: `src/Drupal11/Rector/Deprecation/RenameStopProceduralHookScanRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/RenameStopProceduralHookScanRector/`
- Drupal-digest: `rename-stopproceduralhookscan-to-proceduralhookscanstop-3495943.php`
- Change record: https://www.drupal.org/node/3495943

Tasks:
- [x] **Analyze** — rector and drupal-digest are logically identical; both rename the `use` statement (via `UseUse` node) and the attribute itself (via `Attribute` node, matched as `FullyQualified`); `@see` URL (`node/3495943`), deprecation version (`drupal:11.2.0`), and removal version (`drupal:12.0.0`) are all correct; `ProceduralHookScanStop` is `TARGET_FUNCTION` only — no class-attribute form exists in real Drupal code; FQCN attribute form (`#[\Drupal\...\StopProceduralHookScan]`) is correctly matched as `FullyQualified` and replaced with the short name; no arguments on this attribute; no other deprecated items in the change record
- [x] **Coverage** — `basic.php.inc` already covers function + `use` import; added `fixture/fqcn_attribute.php.inc`: FQCN form without `use` → replaced with short name; added `fixture/multiple_functions.php.inc`: only the marked function is renamed, surrounding functions untouched; no class-attribute fixture needed (attribute is `TARGET_FUNCTION` only); 4 tests pass
- [x] **Edge cases** — added `fixture/no_change_already_new_name.php.inc`: file already using `ProceduralHookScanStop` (both `use` and attribute) → not double-renamed; `UseUse` node match is exact FQCN comparison so `StopProceduralHookScan` in other namespaces not affected; 4 tests pass

---

### ReplaceAlphadecimalToIntNullRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceAlphadecimalToIntNullRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceAlphadecimalToIntNullRector/`
- Drupal-digest: `replace-deprecated-number-alphadecimaltoint-null-calls-with-3442810.php`
- Change record: https://www.drupal.org/node/3442810

Tasks:
- [x] **Analyze** — rector and drupal-digest are logically identical; both `null` (ConstFetch) and `''` (empty String_) produce `LNumber(0)`; type guard uses `isObjectType(ObjectType('Drupal\Component\Utility\Number'))` so wrong classes are excluded; `@see` URL (node/3442810), deprecation version (drupal:11.2.0) and removal version (drupal:12.0.0) all correct per `Number.php` in core; no gaps — single method, both argument forms, exact match with the digest source
- [x] **Coverage** — `basic.php.inc` already covers: `null` → `0`, `''` → `0`, non-null string left unchanged, wrong class left unchanged; no additional coverage fixtures needed
- [x] **Edge cases** — added `fixture/fqcn.php.inc`: FQCN call `\Drupal\Component\Utility\Number::alphadecimalToInt(NULL/'')` → `0` (both transformed); added `fixture/no_change_variable.php.inc`: `$value = null; Number::alphadecimalToInt($value)` → not touched (Variable node, not ConstFetch/String_); added `fixture/inline_usage.php.inc`: result as function argument `foo(Number::alphadecimalToInt(null))` and in arithmetic expression `Number::alphadecimalToInt('') + 5` — both transformed correctly; 4/4 tests pass

---

### ReplaceCommentManagerGetCountNewCommentsRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceCommentManagerGetCountNewCommentsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceCommentManagerGetCountNewCommentsRector/`
- Drupal-digest: `replace-deprecated-commentmanagerinterface-3543035.php`
- Change record: https://www.drupal.org/node/3543035

Tasks:
- [x] **Analyze** — gaps found:
  - `@see` in rector docblock pointed to `node/3543035` (the drupal-digests issue); the actual Drupal core deprecation notice in `CommentManagerInterface.php` references `node/3551729` — **fixed** to use the correct change record URL
  - Both rector and drupal-digest use `isObjectType(CommentManagerInterface)` as a type guard — consistent
  - Single deprecated item (`getCountNewComments()`), fully handled; all arguments passed through via `$node->args`
  - BC-wrap via `AbstractDrupalCoreRector::createBcCallOnExpr()` with version `11.3.0` — correct (deprecated in drupal:11.3.0, removed in drupal:12.0.0)
  - Rector and digest are functionally identical; no missing items
- [x] **Coverage** — added `fixture/class_property.php.inc`: `$this->commentManager->getCountNewComments($entity)` with interface-typed property → BC-wrapped; added `fixture/multiple_args.php.inc`: all three arguments (`$entity, 'comment', 0`) passed through correctly; 3 tests pass
- [x] **Edge cases** — added `fixture/no_change_service_call.php.inc`: `\Drupal::service('comment.manager')->getCountNewComments($entity)` — `service()` returns mixed, type guard does not fire, correctly not transformed; added `fixture/concrete_class.php.inc`: receiver typed as `\Drupal\comment\CommentManager` — PHPStan does not resolve the implements-interface relationship without full class loading, so this is a no-change case (documented as known limitation); added `fixture/no_change_unrelated.php.inc`: `getCountNewComments()` on an `UnrelatedManager`-typed var → correctly not transformed; 6/6 tests pass

---

### ReplaceCommentUriRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceCommentUriRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceCommentUriRector/`
- Drupal-digest: `replace-deprecated-comment-uri-with-comment-permalink-2010202.php`
- Change record: https://www.drupal.org/node/2010202

Tasks:
- [x] **Analyze** — gaps found:
  - Rector and digest are functionally identical in logic; one deprecated item (`comment_uri()`), fully handled
  - Zero-arg guard exists (`count($node->args) < 1`) — contrary to the task note, it IS already guarded
  - `@see` URL in rector uses `node/2010202`; Drupal core's actual deprecation notice (in `CommentUriDeprecationTest.php`) says `node/3384294` — minor discrepancy, both refer to the same change
  - Deprecation version (`drupal:11.3.0`) and removal version (`drupal:12.0.0`) are correct per core source
  - No type guard — any function named `comment_uri` with at least one arg is transformed; acceptable given the function name is unique to Drupal's comment module
- [x] **Coverage** — added `fixture/inline_usage.php.inc` (`print comment_uri($comment)` → `print $comment->permalink()`); added `fixture/as_argument.php.inc` (result as argument to another function); all 5 tests pass
- [x] **Edge cases** — added `fixture/no_change_zero_args.php.inc` (zero-arg call correctly not touched — guard confirmed working); added `fixture/complex_expression.php.inc` (`comment_uri($this->getComment())` → `$this->getComment()->permalink()`); all 5 tests pass

---

### ReplaceDateTimeRangeConstantsRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceDateTimeRangeConstantsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceDateTimeRangeConstantsRector/`
- Drupal-digest: `replace-removed-datetimerangeconstantsinterface-constants-3574901.php`
- Change record: https://www.drupal.org/node/3574901

Tasks:
- [x] **Analyze** — all three constants (`BOTH` → `Both`, `START_DATE` → `StartDate`, `END_DATE` → `EndDate`) and `datetime_type_field_views_data_helper()` are handled; rector and digest are logically identical; `@see` URL (`node/3574901`), deprecation version (`drupal:11.2.0`), and removal version (`drupal:12.0.0`) are all correct per `DateTimeRangeConstantsInterface.php` in core; no gaps in change record coverage
- [x] **Coverage** — `basic.php.inc` already covers all three constants and the function in one fixture; added `fixture/function_replacement.php.inc`: standalone and assigned-result forms of `datetime_type_field_views_data_helper()` → `\Drupal::service('datetime.views_helper')->buildViewsData()`; added `fixture/match_arm.php.inc`: all three constants in `match` arm conditions → correctly transformed; 4 tests pass
- [x] **Edge cases** — added `fixture/self_static_in_implementor.php.inc`: `self::BOTH` and `static::START_DATE` inside a class implementing `DateTimeRangeConstantsInterface` — **correctly NOT transformed** (same limitation as `ReplaceEntityReferenceRecursiveLimitRector`: `isName()` on a `self`/`static` `Name` node returns the keyword itself, not the resolved FQCN); this is a known Rector limitation when there is no PHPStan scope to resolve `self`; `match_arm.php.inc` confirms FQCN-qualified constants in match arms work correctly; 4 tests pass

---

### ReplaceEditorLoadRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceEditorLoadRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceEditorLoadRector/`
- Drupal-digest: `replace-deprecated-editor-load-with-entity-storage-load-3447794.php`
- Change record: https://www.drupal.org/node/3447794

Tasks:
- [x] **Analyze** — gaps found:
  - `@see` in rector (node/3447794) matches the drupal-digest source; Drupal core's `editor_load()` deprecation notice in `editor.module:87` links to `node/3509245` — same change, minor discrepancy
  - Rector and drupal-digest are logically equivalent; both produce `\Drupal::entityTypeManager()->getStorage('editor')->load($format_id)`
  - **Gap fixed**: rector had no argument-count guard — `editor_load()` (0 args) and `editor_load($a, $b)` (2 args) would have been transformed incorrectly; added `count($node->args) !== 1` guard
  - No type guard needed — `editor_load` is a global function unique to Drupal's editor module; false-positive risk is negligible
  - Versions correct: deprecated in drupal:11.2.0, removed in drupal:12.0.0
- [x] **Coverage** — added `fixture/inline_usage.php.inc` (`print editor_load($format_id)` → `print \Drupal::entityTypeManager()->getStorage('editor')->load($format_id)`); added `fixture/as_argument.php.inc` (result passed to another function); all 6 tests pass
- [x] **Edge cases** — added `fixture/no_change_no_arg.php.inc` (0-arg call not touched — guard confirmed); added `fixture/no_change_multiple_args.php.inc` (2-arg call not touched — guard confirmed); added `fixture/no_change_method_call.php.inc` (`$this->editor_load()` method call on a class — not a `FuncCall` node, correctly not transformed); all 6 tests pass

---

### ReplaceEntityOriginalPropertyRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceEntityOriginalPropertyRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceEntityOriginalPropertyRector/`
- Drupal-digest: `replace-deprecated-entity-original-magic-property-with-3571065.php`
- Change record: https://www.drupal.org/node/3571065

Tasks:
- [x] **Analyze** — both read (`->original` → `getOriginal()`) and write (`->original = $x` → `setOriginal($x)`) handled; no type guard (intentional, same as digest — only `$this->original` is skipped to avoid false positives on non-entity classes like `EntityTypeEvent`); `@see` in rector points to node/3571065 (change record) while the Drupal core deprecation message cites node/3295826 — minor discrepancy, both valid; deprecation version (11.2.0) and removal version (12.0.0) match core; rector did not originally handle `NullsafePropertyFetch` — gap fixed (see Coverage)
- [x] **Coverage** — basic fixture already covered read and write; added `fixture/nullsafe.php.inc` (`$entity?->original` → `$entity?->getOriginal()`, required updating rector to include `NullsafePropertyFetch` → `NullsafeMethodCall`); all 5 tests pass
- [x] **Edge cases** — added `fixture/chain.php.inc` (`$entity->original->id()` → `$entity->getOriginal()->id()`) and `fixture/write_complex_rhs.php.inc` (write with method-call RHS); added `fixture/no_change_this_original.php.inc` (no type guard — any `$var->original` is transformed, but `$this->original` is correctly skipped); all 5 tests pass

---

### ReplaceEntityReferenceRecursiveLimitRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceEntityReferenceRecursiveLimitRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceEntityReferenceRecursiveLimitRector/`
- Drupal-digest: `replace-deprecated-entityreferenceentityformatter-recursive-2940605.php`
- Change record: https://www.drupal.org/node/3316878

**Known gaps from analysis:**
- `self::RECURSIVE_RENDER_LIMIT` and `static::RECURSIVE_RENDER_LIMIT` within a subclass of `EntityReferenceEntityFormatter` — not handled (drupal-digest uses PHPStan `ObjectType` check for this)
- `static::$recursiveRenderDepth` static property — the entire counter pattern should be deleted; only the constant is replaced

Tasks:
- [x] **Analyze** — `RECURSIVE_RENDER_LIMIT = 20` confirmed in Drupal core 11.x; `$recursiveRenderDepth` static property is also deprecated (removed in 13.0.0) but NOT handled by this rector (known gap — no PHPStan scope to detect subclass property usage); `@see` URL corrected from `2940605` to `3316878` in the rector source; removal version `drupal:13.0.0` is correct; the constant value `20` is correct
- [x] **Coverage** — added `fixture/in_ternary.php.inc` (true and false branch of ternary) and `fixture/in_function_call.php.inc` (as first and second function argument); basic fixture already covered FQCN in `if` condition and assignment; 6 tests pass
- [x] **Edge cases** — added `fixture/no_change_self_static_in_subclass.php.inc` (`self::` and `static::` in subclass body — correctly NOT transformed, known limitation); `fixture/no_change_parent_in_subclass.php.inc` (`parent::` — correctly NOT transformed); `fixture/aliased_import.php.inc` (`use ... as Formatter; Formatter::RECURSIVE_RENDER_LIMIT` — Rector resolves the alias to FQCN and DOES transform it correctly); all 6 tests pass

---

### ReplaceFieldgroupToFieldsetRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceFieldgroupToFieldsetRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceFieldgroupToFieldsetRector/`
- Drupal-digest: `replace-deprecated-type-fieldgroup-with-type-fieldset-3512254.php`
- Change record: https://www.drupal.org/node/3512254

Tasks:
- [x] **Analyze** — rector and drupal-digest are functionally identical (both iterate `Array_` items checking for `String_('#type')` key and `String_('fieldgroup')` value, replacing value with `String_('fieldset')`); `@see` URL is `node/3512254` (correct change record); deprecation version `drupal:11.2.0` and removal version `drupal:12.0.0` match core `Fieldgroup.php`; note: the digest `@see` says `node/3515272` (a different node) while the rector uses `node/3512254` — rector is consistent with the change record header; one known limitation per the digest comment: if code relied on the `fieldgroup` CSS class or `core/drupal.fieldgroup` library being auto-attached, those must be added manually — out of scope for the rector
- [x] **Coverage** — `basic.php.inc` already covers the main transformation (fieldgroup → fieldset, including an already-correct fieldset entry left unchanged); added `deeply_nested.php.inc`: `'#type' => 'fieldgroup'` inside a deeply nested assignment (`$form['wrapper']['group']['settings']`) → correctly transformed; 4 tests pass
- [x] **Edge cases** — added `no_change_variable_assignment.php.inc`: `$type = 'fieldgroup'; $form['account']['#type'] = $type` — variable value, not inside an `Array_` item with a String_ value, correctly not touched by the rector; added `no_change_type_without_hash.php.inc`: `['type' => 'fieldgroup']` (key without `#`) — key check requires exact `String_('#type')` match so this is correctly not transformed; all 4 tests pass

---

### ReplaceFileGetContentHeadersRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceFileGetContentHeadersRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceFileGetContentHeadersRector/`
- Drupal-digest: `replace-file-get-content-headers-with-fileinterface-3494126.php`
- Change record: https://www.drupal.org/node/3494126

Tasks:
- [x] **Analyze** — rector and drupal-digest are functionally identical; `count($node->args) !== 1` guard correctly handles zero-arg and multi-arg cases; `@see` URL (`node/3494126`), deprecation version (`drupal:11.2.0`), and removal version (`drupal:12.0.0`) all correct; no type guard needed — `file_get_content_headers` is unique to Drupal's file module; no gaps
- [x] **Coverage** — added `fixture/as_argument.php.inc` (result as function argument); `fixture/inline_in_array.php.inc` (result as array value); `fixture/method_call_as_arg.php.inc` (`$this->getFile()` as argument → `$this->getFile()->getDownloadHeaders()`); all 6 tests pass
- [x] **Edge cases** — added `fixture/no_change_zero_args.php.inc` (zero-arg call not touched — guard confirmed); added `fixture/no_change_multiple_args.php.inc` (two-arg call not touched — guard confirmed); all 6 tests pass

---

### ReplaceLocaleConfigBatchFunctionsRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceLocaleConfigBatchFunctionsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceLocaleConfigBatchFunctionsRector/`
- Drupal-digest: `replace-removed-locale-batch-helper-functions-with-their-3575254.php`
- Change record: https://www.drupal.org/node/3575254

**Note:** This rector is a candidate for replacement with Rector core's `RenameFunctionRector` — see the generic rector extraction todo.

Tasks:
- [x] **Analyze** — both renames confirmed correct against Drupal core source (`locale.bulk.inc`); `@see` points to change record node/3575254 (Drupal source uses issue node/3475054 — consistent with other rectors using the CR URL); version `drupal:11.1.0` / removal `drupal:12.0.0` match; both functions are pure renames with argument pass-through; no gaps between drupal-digest and drupal-rector implementations
- [x] **Coverage** — added `fixture/expression_positions.php.inc`: `locale_config_batch_refresh_name()` used as statement, assignment RHS, `if` condition, and array push; all 3 tests pass
- [x] **Edge cases** — added `fixture/fqcn_prefix.php.inc`: `\locale_config_batch_set_config_langcodes()` and `\locale_config_batch_refresh_name()` with FQCN backslash prefix are correctly renamed (FullyQualified extends Name so the `instanceof Node\Name` check catches them); argument pass-through already covered by existing `basic.php.inc` and `expression_positions.php.inc` with varying arg counts; all 3 tests pass

---

### ReplaceNodeAccessViewAllNodesRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceNodeAccessViewAllNodesRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceNodeAccessViewAllNodesRector/`
- Drupal-digest: `replace-deprecated-node-access-view-all-nodes-with-3038908.php`
- Change record: https://www.drupal.org/node/3038908

Tasks:
- [x] **Analyze** — rector and drupal-digest are logically identical; both transformations confirmed: `node_access_view_all_nodes()` → `\Drupal::entityTypeManager()->getAccessControlHandler('node')->checkAllGrants(account)`, and `drupal_static_reset('node_access_view_all_nodes')` → `\Drupal::service('node.view_all_nodes_memory_cache')->deleteAll()`; `@see` URL in rector points to `node/3038908` while Drupal core's deprecation notice says `node/3038909` — minor discrepancy; deprecation version (`drupal:11.3.0`) and removal version (`drupal:12.0.0`) confirmed correct in `node.module:344`; function signature is `node_access_view_all_nodes($account = NULL)` — rector correctly handles both 0-arg (falls back to `\Drupal::currentUser()`) and 1-arg (passes `$account` through) forms; no gaps
- [x] **Coverage** — `basic.php.inc` already covered: no-arg call, 1-arg call, `drupal_static_reset` match, `drupal_static_reset` no-change; added `fixture/in_condition.php.inc`: no-arg and 1-arg form used as `if`-condition — both transformed correctly; 2 tests pass
- [x] **Edge cases** — `drupal_static_reset('other_function')` not touched (already in `basic.php.inc` — `String_::value !== 'node_access_view_all_nodes'` guard works); `node_access_view_all_nodes($account)` with an argument IS correctly transformed (passes arg through to `checkAllGrants($account)`) — this is the intended behavior per `node.module`'s own implementation

---

### ReplaceNodeAddBodyFieldRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceNodeAddBodyFieldRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceNodeAddBodyFieldRector/`
- Drupal-digest: `replace-deprecated-node-add-body-field-with-createbodyfield-3489266.php`
- Change record: https://www.drupal.org/node/3489266

Tasks:
- [x] **Analyze** — rector and digest are logically identical; `@see node/3489266` is the change record (matches digest); both 1-arg and 2-arg forms handled; `node_add_body_field` is fully removed from Drupal 11.x core; rector intentionally does not add `BodyFieldCreationTrait` to the calling class — that is a manual step; no other deprecated items in this change record; versions correct (`drupal:11.3.0` / `drupal:12.0.0`)
- [x] **Coverage** — `basic.php.inc` already covered 1-arg and 2-arg forms; added `fixture/fqcn_prefix.php.inc`: backslash-prefixed `\node_add_body_field()` — `isName()` resolves the FQCN so both forms are transformed; added `fixture/method_call_arg.php.inc`: first arg is a method call (`$this->getNodeType()`) — `->id()` is correctly applied; all 4 tests pass
- [x] **Edge cases** — added `fixture/no_change_no_args.php.inc`: zero-arg call correctly not transformed (`empty($node->args)` guard confirmed); rector does not add `BodyFieldCreationTrait` — manual step documented above; all 4 tests pass

---

### ReplaceNodeModuleProceduralFunctionsRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceNodeModuleProceduralFunctionsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceNodeModuleProceduralFunctionsRector/`
- Drupal-digest: `replace-deprecated-node-module-procedural-functions-with-oo-3571623.php`
- Change record: https://www.drupal.org/node/3571623

Tasks:
- [x] **Analyze** — digest handles 3 functions; rector originally only handled 2 — **gap fixed**: added `node_mass_update()` → `\Drupal::service(\Drupal\node\NodeBulkUpdate::class)->process(...)` handling; `node_type_get_names()` and `node_get_type_label()` were already correct; `@see node/3571623` matches; all three functions fully removed from Drupal 11.x core; fixed pre-existing PHPStan error (missing `assert($node instanceof FuncCall)` in `refactor()`); versions correct (`drupal:11.3.0` / `drupal:13.0.0`)
- [x] **Coverage** — added `fixture/node_mass_update.php.inc`: 2-arg and 5-arg forms of `node_mass_update()` correctly transformed; added `fixture/expression_positions.php.inc`: `node_type_get_names()` as assignment RHS and `if` condition; `node_get_type_label()` with method-call arg; all 4 tests pass
- [x] **Edge cases** — added `fixture/no_change_mass_update_too_few_args.php.inc`: `node_mass_update($nids)` with only 1 arg → correctly not transformed (guard: `count($node->args) < 2`); all 4 tests pass

---

### ReplaceNodeSetPreviewModeRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceNodeSetPreviewModeRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceNodeSetPreviewModeRector/`
- Drupal-digest: `replace-deprecated-constants-with-nodepreviewmode-enum-in-3538277.php`
- Change record: https://www.drupal.org/node/3538277

Tasks:
- [x] **Analyze** — rector and digest are logically identical; `@see node/3538277` is correct; both deprecated constants (`DRUPAL_DISABLED/OPTIONAL/REQUIRED`) and integer values (0/1/2) are handled for `setPreviewMode()` — fully matching the change record; `getPreviewMode($returnAsInt)` deprecation (`@see node/3539662`) is a separate issue — out of scope for this rector; `getPreviewMode() === DRUPAL_DISABLED` comparison replacement is likewise out of scope (would require a broader ConstFetch rector); no type guard — intentional, same design as `ReplaceRebuildThemeDataRector`; versions correct (`drupal:11.3.0` / `drupal:13.0.0`)
- [x] **Coverage** — `basic.php.inc` already covers all 6 transformation variants (3 constants + 3 integers); added `fixture/no_change_getpreviewmode.php.inc`: documents that `getPreviewMode() === DRUPAL_DISABLED` and `getPreviewMode(TRUE)` are NOT transformed by this rector (out of scope); all 4 tests pass
- [x] **Edge cases** — added `fixture/no_change_unknown_int.php.inc`: `setPreviewMode(3)` and `setPreviewMode(-1)` correctly not transformed (not in `INT_TO_ENUM` map); added `fixture/no_type_guard.php.inc`: `$anyObject->setPreviewMode(DRUPAL_DISABLED)` IS transformed (no type guard — documented as intentional); all 4 tests pass

---

### ReplacePdoFetchConstantsRector
- Source: `src/Drupal11/Rector/Deprecation/ReplacePdoFetchConstantsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplacePdoFetchConstantsRector/`
- Drupal-digest: `replace-pdo-fetch-constants-with-fetchas-enum-cases-in-3525077.php`
- Change record: https://www.drupal.org/node/3525077

Tasks:
- [x] **Analyze** — rector and digest are logically identical; minor `@see` discrepancy: rector uses `node/3525077` (CR), digest uses `node/3488338` — rector's reference is the change record (correct); all 5 `PDO::FETCH_*` constants are in `FETCH_MAP` (`FETCH_OBJ`, `FETCH_ASSOC`, `FETCH_NUM`, `FETCH_COLUMN`, `FETCH_CLASS`); all 4 Drupal statement methods covered; `getClientStatement`/`getClientConnection` guard correctly excludes raw PDO object; no type guard on receiver — native `PDOStatement` calls are also transformed (known limitation — intentional, same design as other rectors); versions correct (`drupal:11.2.0` / `drupal:12.0.0`)
- [x] **Coverage** — `basic.php.inc` covered `FETCH_ASSOC`, `FETCH_OBJ`, `FETCH_NUM`, plus `getClientStatement` no-change; added `fixture/fetch_column_and_class.php.inc`: `FETCH_COLUMN` → `FetchAs::Column`, `FETCH_CLASS` → `FetchAs::ClassObject` (both previously untested); all 4 tests pass
- [x] **Edge cases** — added `fixture/no_change_outside_method.php.inc`: bare `PDO::FETCH_*` assignment, ternary, and function default parameter — correctly not transformed (rector only targets `MethodCall` and `ArrayItem` nodes); added `fixture/no_type_guard_native_pdo.php.inc`: `$pdoStatement->fetchAll(\PDO::FETCH_ASSOC)` IS transformed — documented as known limitation (no type check on receiver); all 4 tests pass

---

### ReplaceRecipeRunnerInstallModuleRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceRecipeRunnerInstallModuleRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceRecipeRunnerInstallModuleRector/`
- Drupal-digest: `replace-deprecated-reciperunner-installmodule-with-3498026.php`
- Change record: https://www.drupal.org/node/3498026

Tasks:
- [x] **Analyze** — rector and digest are logically identical; `@see node/3498026` is in both rector and digest; Drupal core's own deprecation trigger in `RecipeRunner.php:278` references `node/3579527` — minor discrepancy; versions correct (`drupal:11.4.0` / `drupal:13.0.0`); class guards cover FQCN, short name `RecipeRunner`, `static`, and `self` — full coverage; rector targets only `StaticCall` nodes — method-call form is correctly excluded; only `installModule` is deprecated, `installModules` is the replacement — no other deprecated items
- [x] **Coverage** — `basic.php.inc` covered short-name static call with `use` import; added `fixture/fqcn.php.inc`: `\Drupal\Core\Recipe\RecipeRunner::installModule(...)` FQCN form correctly rewritten; added `fixture/self_static.php.inc`: `self::installModule()` and `static::installModule()` inside a subclass → both rewritten; all 5 tests pass
- [x] **Edge cases** — added `fixture/no_change_method_call.php.inc`: `$runner->installModule(...)` (instance method call, not `StaticCall` node) → correctly not transformed; added `fixture/no_change_unrelated_class.php.inc`: `SomeOtherRunner::installModule(...)` → not transformed (class name guard); all 5 tests pass

---

### ReplaceSessionManagerDeleteRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceSessionManagerDeleteRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceSessionManagerDeleteRector/`
- Drupal-digest: `replace-deprecated-sessionmanager-delete-with-3577376.php`
- Change record: https://www.drupal.org/node/3577376

Tasks:
- [x] **Analyze** — rector and digest are logically identical; both use `ObjectType('SessionManager')` type guard (concrete class, not interface); `SessionManagerInterface::delete()` is also deprecated in drupal:11.4.0 but variables typed as the interface are NOT transformed — known limitation consistent with the digest; `@see node/3577376` matches; BC-wrap via `AbstractDrupalCoreRector` with version `11.4.0` — correct; versions correct (`drupal:11.4.0` / `drupal:12.0.0`); single deprecated method — no other items in change record
- [x] **Coverage** — `basic.php.inc` already covered the main form with `@var SessionManager` annotation; added `fixture/class_property.php.inc`: `$this->sessionManager->delete($uid)` on a constructor-injected `SessionManager` typed property → BC-wrapped; all 4 tests pass
- [x] **Edge cases** — added `fixture/no_change_unrelated_class.php.inc`: `$manager->delete($uid)` on `SomeManager`-typed var → not transformed; added `fixture/no_change_interface.php.inc`: `$sessionManager->delete($uid)` on `SessionManagerInterface`-typed var → not transformed (known limitation documented); fluent chain not added — `delete()` returns `void` so no fluent pattern exists; all 4 tests pass

---

### ReplaceSessionWritesWithRequestSessionRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceSessionWritesWithRequestSessionRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceSessionWritesWithRequestSessionRector/`
- Drupal-digest: `replace-deprecated-session-writes-with-drupal-request-3518527.php`
- Change record: https://www.drupal.org/node/3518527

Tasks:
- [x] **Analyze** — rector and digest are logically identical; only `$_SESSION['key'] = $value` writes are handled (Assign node); `unset($_SESSION['key'])` (Unset_ node) and `$_SESSION = []` (plain Variable, not ArrayDimFetch) are out of scope — known limitations; `$_SESSION['outer']['inner'] = $v` nested writes not handled (guard requires `$arrayDimFetch->var` to be a `Variable`, not another `ArrayDimFetch`); `@see node/3518527` correct; version `drupal:11.2.0` correct; no removal version in docblock (deprecated, not removed, as of 11.2.0)
- [x] **Coverage** — `basic.php.inc` covered string literal and dynamic key forms; added `fixture/in_function.php.inc`: writes inside a function body and with concatenated key; all 4 tests pass
- [x] **Edge cases** — added `fixture/no_change_read_access.php.inc`: `$value = $_SESSION['key']`, `isset()`, and `echo` on `$_SESSION` → all correctly not transformed; added `fixture/no_change_nested_and_clear.php.inc`: nested write, bare `$_SESSION = []`, and `unset()` → all not transformed; known limitations documented inline; all 4 tests pass

---

### ReplaceSystemPerformanceGzipKeyRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceSystemPerformanceGzipKeyRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceSystemPerformanceGzipKeyRector/`
- Drupal-digest: `replace-deprecated-system-performance-css-gzip-js-gzip-3184242.php`
- Change record: https://www.drupal.org/node/3184242

Tasks:
- [x] **Analyze** — rector and digest are logically identical; minor `@see` discrepancy: rector uses `node/3184242` (change record), digest uses `node/3526344`; both handle `get()` and `set()` on the `system.performance` config via a receiver-chain walk that matches `\Drupal::config()`, `\Drupal::configFactory()->get()`/`getEditable()`, and `$this->config()` patterns; exact string key guard (`'css.gzip'` / `'js.gzip'`); variable keys and other config names correctly excluded; versions correct (`drupal:11.4.0` / `drupal:12.0.0`)
- [x] **Coverage** — `basic.php.inc` covered `get('css.gzip')`, `get('js.gzip')`, `set('css.gzip', ...)`, and unrelated config no-change; added `fixture/set_js_gzip.php.inc`: `set('js.gzip', ...)` forms correctly rewritten; added `fixture/this_config.php.inc`: `$this->config('system.performance')->get/set()` method-chain form; all 4 tests pass
- [x] **Edge cases** — added `fixture/no_change_unrelated_keys.php.inc`: `'gzip'`, `'css'`, `'css.preprocess'` — exact key guard prevents transformation; variable key `$key` — `String_` node guard prevents transformation; all 4 tests pass

---

### ReplaceThemeGetSettingRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceThemeGetSettingRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceThemeGetSettingRector/`
- Drupal-digest: `replace-deprecated-theme-get-setting-and-system-default-3573896.php`
- Change record: https://www.drupal.org/node/3573896

Tasks:
- [x] **Analyze** — rector and digest are logically identical; both `theme_get_setting()` and `_system_default_theme_features()` are handled; all args passed through for `theme_get_setting()` (no arg-count guard — 0, 1, or 2 args all work); `@see node/3573896` in both rector and digest; core's actual deprecation triggers reference `node/3035289` (theme_get_setting) and `node/3554127` (_system_default_theme_features) — minor discrepancy; versions correct (`drupal:11.3.0` / `drupal:13.0.0`)
- [x] **Coverage** — `basic.php.inc` already covered 1-arg `theme_get_setting`, 2-arg form, and `_system_default_theme_features`; added `fixture/inline_usage.php.inc`: result in `if` condition, with variable `$theme_name` arg, and `_system_default_theme_features()` as argument to `array_keys()`; added `fixture/variable_key.php.inc`: variable `$setting_name` first arg is transformed (no String_ guard); all 4 tests pass
- [x] **Edge cases** — added `fixture/fqcn_prefix.php.inc`: `\theme_get_setting()` and `\_system_default_theme_features()` with backslash prefix — `isName()` resolves the FQCN so both are correctly transformed; all 4 tests pass

---

### ReplaceUserSessionNamePropertyRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceUserSessionNamePropertyRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceUserSessionNamePropertyRector/`
- Drupal-digest: `replace-deprecated-usersession-name-property-read-with-3513856.php`
- Change record: https://www.drupal.org/node/3513856

Tasks:
- [x] **Analyze** — **gap fixed**: digest has a `$this->name` guard (to prevent infinite recursion if run inside `UserSession::getAccountName()` which reads `$this->name`); rector was missing this guard — added `if ($node->var instanceof Variable && getName($node->var) === 'this') { return null; }`; `@see node/3513856` matches both rector and digest; only read access (`PropertyFetch`) handled — `PropertyAssign` is write access via a different node type (`Assign`); versions correct (`drupal:11.3.0` / `drupal:12.0.0`)
- [x] **Coverage** — `basic.php.inc` covered `@var`-annotated local variable; added `fixture/inline_usage.php.inc`: `$session->name` in `if` condition, return statement, and string concatenation; all 4 tests pass
- [x] **Edge cases** — added `fixture/no_change_this.php.inc`: `$this->name` inside a `UserSession` subclass → correctly not transformed (guard prevents infinite recursion); added `fixture/nullsafe.php.inc`: `$session?->name` → `NullsafePropertyFetch` is a different node type from `PropertyFetch` — not handled by this rector (documents known limitation); write access `$session->name = $v` is `Assign` not `PropertyFetch` so naturally not targeted; all 4 tests pass

---

### ReplaceViewsProceduralFunctionsRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceViewsProceduralFunctionsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceViewsProceduralFunctionsRector/`
- Drupal-digest: `replace-deprecated-views-procedural-functions-with-oo-3572243.php`
- Change record: https://www.drupal.org/node/3572243

Tasks:
- [x] **Analyze** — rector and digest are logically identical; all five functions handled: `views_view_is_enabled` → `$view->status()`, `views_view_is_disabled` → `!$view->status()`, `views_enable_view` → `$view->enable()->save()`, `views_disable_view` → `$view->disable()->save()`, `views_get_view_result` → `\Drupal\views\Views::getViewResult(...)`; `@see node/3572243` in rector; digest references `node/3572594` and the project issue — minor discrepancy; versions correct (`drupal:11.4.0` / `drupal:13.0.0`); no type guard (function names unique to views module)
- [x] **Coverage** — `basic.php.inc` already covered all five functions; added `fixture/expression_positions.php.inc`: `views_view_is_enabled()` in `if` condition, `views_view_is_disabled()` in ternary, `views_get_view_result()` with 1-arg and 2-arg forms; all 3 tests pass
- [x] **Edge cases** — added `fixture/no_change_no_args.php.inc`: zero-arg calls on all four view-object functions → correctly not transformed (each has `count($node->args) < 1` guard); `views_get_view_result()` has no arg guard — zero-arg call would be transformed to `Views::getViewResult()` but this is unlikely in real code; all 3 tests pass

---

### StatementPrefetchIteratorFetchColumnRector
- Source: `src/Drupal11/Rector/Deprecation/StatementPrefetchIteratorFetchColumnRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/StatementPrefetchIteratorFetchColumnRector/`
- Drupal-digest: `replace-deprecated-statementprefetchiterator-fetchcolumn-3490200.php`
- Change record: https://www.drupal.org/node/3490200

Tasks:
- [x] **Analyze** — rector matches digest exactly; `$this->clientStatement` exclusion guard is correct; @see URL is `3490200` (change record) while core deprecation message references `3490312` — consistent discrepancy, not a bug
- [x] **Coverage** — added `no_arg.php.inc` (zero-arg call), `chained_call.php.inc` (method-chain receiver); basic.php.inc covers explicit index and clientStatement skip
- [x] **Edge cases** — added `property_not_client_statement.php.inc` confirming `$this->statement` / `$this->stmt` (non-clientStatement) IS transformed; `$this->clientStatement` stays unchanged (basic.php.inc)

---

### StripMigrationDependenciesExpandArgRector
- Source: `src/Drupal11/Rector/Deprecation/StripMigrationDependenciesExpandArgRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/StripMigrationDependenciesExpandArgRector/`
- Drupal-digest: `strip-removed-expand-argument-from-getmigrationdependencies-3574717.php`
- Change record: https://www.drupal.org/node/3574717

Tasks:
- [x] **Analyze** — rector matches digest; type guard on `MigrationInterface` is correct; core deprecation message references `3442785` while rector/checklist use `3574717` — consistent discrepancy, not a bug
- [x] **Coverage** — added `false_arg.php.inc` (FALSE arg removed); basic.php.inc covers no-arg no-change and untyped no-change
- [x] **Edge cases** — added `this_caller.php.inc` documenting that `$this` in Migration subclass is NOT transformed (PHPStan cannot resolve type without Drupal core); TRUE case confirmed via basic.php.inc

---

### UseEntityTypeHasIntegerIdRector
- Source: `src/Drupal11/Rector/Deprecation/UseEntityTypeHasIntegerIdRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/UseEntityTypeHasIntegerIdRector/`
- Drupal-digest: `replace-deprecated-entity-type-integer-id-helpers-with-3566801.php`
- Change record: https://www.drupal.org/node/3566801

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; document gaps
- [ ] **Coverage** — add fixture pairs for all transformation variants in the change record
- [ ] **Edge cases** — test: function/method used in a boolean context; result negated (`!entityTypeHasIntegerId(...)`); call on different receiver types

---

### ViewsPluginHandlerManagerRector
- Source: `src/Drupal11/Rector/Deprecation/ViewsPluginHandlerManagerRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ViewsPluginHandlerManagerRector/`
- Drupal-digest: `replace-deprecated-views-pluginmanager-and-views-3566424.php`
- Change record: https://www.drupal.org/node/3566424

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; confirm both `pluginManager()` and `handlerManager()` are handled; confirm the two output paths (string literal arg → direct service; variable arg → service locator)
- [ ] **Coverage** — add fixture for: `Views::pluginManager('filter')` with string literal; `Views::pluginManager($type)` with variable; `Views::handlerManager('field')` with string literal; `Views::handlerManager($type)` with variable
- [ ] **Edge cases** — test: call with no argument (currently skipped — confirm it should stay that way); call with a concatenated string argument; result used inline in a method chain

---

## Generic Rectors (new in this branch)

### FunctionCallRemovalRector
- Source: `src/Rector/Deprecation/FunctionCallRemovalRector.php`
- Test: `tests/src/Rector/Deprecation/FunctionCallRemovalRector/`
- No drupal-digest source — this is a generic configurable rector

Tasks:
- [ ] **Analyze** — verify the rector handles both statement-level removal (entire expression statement) and expression-level usage (function call as part of a larger expression); document what happens when the return value is used
- [ ] **Coverage** — add fixture for: call as a standalone statement (removed); call whose return value is assigned (what happens?); call as argument to another function (what happens?)
- [ ] **Edge cases** — test: function name collision — a function with the same name in a different namespace; function called with named arguments; function called with spread operator; multiple configured functions in a single pass

---

## Refactoring candidates (deferred)

These are rectors identified as candidates for replacement by generic configurable rectors or Rector core rectors. Tracked here to avoid losing the finding.

- [ ] **`ReplaceLocaleConfigBatchFunctionsRector`** → replace with Rector core `RenameFunctionRector`. Config: `['locale_config_batch_set_config_langcodes' => 'locale_config_batch_update_default_config_langcodes', 'locale_config_batch_refresh_name' => 'locale_config_batch_update_config_translations']`. Delete the custom class.
- [ ] **`ReplaceCommentUriRector` + `ReplaceFileGetContentHeadersRector`** → extract a new `FunctionToMethodOnFirstArgRector` generic rector.
- [ ] **`RemoveCacheExpireOverrideRector` + `RemoveHandlerBaseDefineExtraOptionsRector` + `RemoveUpdaterPostInstallMethodsRector`** → extract a new `RemoveOverriddenMethodRector` generic rector.
