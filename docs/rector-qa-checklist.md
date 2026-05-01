# Rector QA Checklist

**Next:** [`ReplaceCommentManagerGetCountNewCommentsRector`](#replacecommentmanagergetcountnewcommentsrector)

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
- [ ] **Analyze** — compare rector against drupal-digest source and change record; document gaps
- [ ] **Coverage** — add fixture pairs for all transformation variants in the change record
- [ ] **Edge cases** — test: call on `$this->commentManager`; call via `\Drupal::service('comment.manager')`; receiver typed as concrete class vs interface

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
- [ ] **Analyze** — compare rector against drupal-digest source and change record; document gaps
- [ ] **Coverage** — add fixture for result used inline (not assigned); result used as argument
- [ ] **Edge cases** — test: call with no argument (should not be touched); call with multiple arguments (should not be touched if the function signature changed); unrelated `editor_load()` in a different namespace context

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
- [ ] **Analyze** — fetch the correct change record at node/3316878 (not 2940605 which is the original bug); confirm both deprecated items (`RECURSIVE_RENDER_LIMIT` constant AND `$recursiveRenderDepth` static property) are documented; note that the canonical fix is to delete counter code, not just replace the constant
- [ ] **Coverage** — add fixture for: FQCN `EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT`; used in a ternary; used as a function argument
- [ ] **Edge cases** — test: `self::RECURSIVE_RENDER_LIMIT` inside a subclass body (currently not matched); `static::RECURSIVE_RENDER_LIMIT` inside a subclass body; aliased import `use EntityReferenceEntityFormatter as Formatter; Formatter::RECURSIVE_RENDER_LIMIT`; `parent::RECURSIVE_RENDER_LIMIT` in a subclass override

---

### ReplaceFieldgroupToFieldsetRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceFieldgroupToFieldsetRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceFieldgroupToFieldsetRector/`
- Drupal-digest: `replace-deprecated-type-fieldgroup-with-type-fieldset-3512254.php`
- Change record: https://www.drupal.org/node/3512254

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; document gaps
- [ ] **Coverage** — add fixture pairs for all transformation variants in the change record
- [ ] **Edge cases** — test: `'#type' => 'fieldgroup'` in a deeply nested array; assignment via variable `$type = 'fieldgroup'; $form['#type'] = $type` (should NOT be touched — only string literal); the key `'#type'` vs just `'type'`

---

### ReplaceFileGetContentHeadersRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceFileGetContentHeadersRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceFileGetContentHeadersRector/`
- Drupal-digest: `replace-file-get-content-headers-with-fileinterface-3494126.php`
- Change record: https://www.drupal.org/node/3494126

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; document gaps
- [ ] **Coverage** — add fixture for: result used as argument to another function; result used inline in an array; the `$file` argument being a method call expression
- [ ] **Edge cases** — test: call with zero arguments (should not be touched); call with additional arguments beyond one (should not be touched if the original function only took one)

---

### ReplaceLocaleConfigBatchFunctionsRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceLocaleConfigBatchFunctionsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceLocaleConfigBatchFunctionsRector/`
- Drupal-digest: `replace-removed-locale-batch-helper-functions-with-their-3575254.php`
- Change record: https://www.drupal.org/node/3575254

**Note:** This rector is a candidate for replacement with Rector core's `RenameFunctionRector` — see the generic rector extraction todo.

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; confirm both function renames are correct
- [ ] **Coverage** — add fixture for `locale_config_batch_refresh_name()` call; result used in various expression positions
- [ ] **Edge cases** — test: call with different argument counts (pass-through); FQCN-prefixed call `\locale_config_batch_set_config_langcodes()`

---

### ReplaceNodeAccessViewAllNodesRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceNodeAccessViewAllNodesRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceNodeAccessViewAllNodesRector/`
- Drupal-digest: `replace-deprecated-node-access-view-all-nodes-with-3038908.php`
- Change record: https://www.drupal.org/node/3038908

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; confirm both the function call AND the `drupal_static_reset()` variant are handled
- [ ] **Coverage** — add fixture for: `node_access_view_all_nodes()` call; `drupal_static_reset('node_access_view_all_nodes')` call; result used in a condition
- [ ] **Edge cases** — test: `drupal_static_reset` with a different argument (should NOT be touched); `node_access_view_all_nodes()` with an argument (should not be touched if signature changed)

---

### ReplaceNodeAddBodyFieldRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceNodeAddBodyFieldRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceNodeAddBodyFieldRector/`
- Drupal-digest: `replace-deprecated-node-add-body-field-with-createbodyfield-3489266.php`
- Change record: https://www.drupal.org/node/3489266

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; document gaps
- [ ] **Coverage** — add fixture pairs for all transformation variants in the change record
- [ ] **Edge cases** — test: call with multiple arguments; call in a context where the trait import needs to be added (if the rector handles this); call result used inline

---

### ReplaceNodeModuleProceduralFunctionsRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceNodeModuleProceduralFunctionsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceNodeModuleProceduralFunctionsRector/`
- Drupal-digest: `replace-deprecated-node-module-procedural-functions-with-oo-3571623.php`
- Change record: https://www.drupal.org/node/3571623

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; list all functions covered; confirm each has a fixture
- [ ] **Coverage** — add fixture pair for each function that does not yet have a dedicated fixture
- [ ] **Edge cases** — test: each function with result used in different expression positions; each function with different argument types/counts

---

### ReplaceNodeSetPreviewModeRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceNodeSetPreviewModeRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceNodeSetPreviewModeRector/`
- Drupal-digest: `replace-deprecated-constants-with-nodepreviewmode-enum-in-3538277.php`
- Change record: https://www.drupal.org/node/3538277

**Known gaps from analysis:**
- `getPreviewMode() === DRUPAL_DISABLED` comparison — not handled; should become `getPreviewMode() === NodePreviewMode::Disabled`
- `getPreviewMode(TRUE)` with the deprecated `$returnAsInt` argument — not handled
- No receiver type guard on `setPreviewMode()`: any class with a `setPreviewMode()` method is modified, not just `NodeTypeInterface` implementors

Tasks:
- [ ] **Analyze** — fetch the change record at node/3538277; confirm the `getPreviewMode()` read-side deprecation and `$returnAsInt` argument removal are documented; assess whether the missing type guard is a correctness risk
- [ ] **Coverage** — add fixture for: `getPreviewMode() === DRUPAL_DISABLED`; `getPreviewMode() === DRUPAL_OPTIONAL`; `getPreviewMode() === DRUPAL_REQUIRED`; `getPreviewMode(TRUE)` argument removal
- [ ] **Edge cases** — test: `setPreviewMode(DRUPAL_DISABLED)` on a non-NodeTypeInterface class (should ideally be guarded); `DRUPAL_DISABLED` constant used in a `switch`/`match` on the result of `getPreviewMode()`; integer `3` passed (not in map, should not be changed); integer `0` outside `setPreviewMode()` context

---

### ReplacePdoFetchConstantsRector
- Source: `src/Drupal11/Rector/Deprecation/ReplacePdoFetchConstantsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplacePdoFetchConstantsRector/`
- Drupal-digest: `replace-pdo-fetch-constants-with-fetchas-enum-cases-in-3525077.php`
- Change record: https://www.drupal.org/node/3525077

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; list all `PDO::FETCH_*` constants and confirm each has a mapping
- [ ] **Coverage** — add fixture pair for each `PDO::FETCH_*` constant that does not yet have a dedicated fixture
- [ ] **Edge cases** — test: constant used as a default parameter value in a function signature; constant used in a ternary; `PDO::FETCH_*` constant used directly on a native PDO object that is not Drupal's wrapper (type guard check)

---

### ReplaceRecipeRunnerInstallModuleRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceRecipeRunnerInstallModuleRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceRecipeRunnerInstallModuleRector/`
- Drupal-digest: `replace-deprecated-reciperunner-installmodule-with-3498026.php`
- Change record: https://www.drupal.org/node/3498026

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; document gaps
- [ ] **Coverage** — add fixture pairs for all transformation variants in the change record
- [ ] **Edge cases** — test: static call vs method call; result used inline; call with different argument types

---

### ReplaceSessionManagerDeleteRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceSessionManagerDeleteRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceSessionManagerDeleteRector/`
- Drupal-digest: `replace-deprecated-sessionmanager-delete-with-3577376.php`
- Change record: https://www.drupal.org/node/3577376

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; document gaps
- [ ] **Coverage** — add fixture pairs for all transformation variants in the change record
- [ ] **Edge cases** — test: `delete()` on an unrelated class is not touched; receiver typed as concrete vs interface; fluent chain after deletion

---

### ReplaceSessionWritesWithRequestSessionRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceSessionWritesWithRequestSessionRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceSessionWritesWithRequestSessionRector/`
- Drupal-digest: `replace-deprecated-session-writes-with-drupal-request-3518527.php`
- Change record: https://www.drupal.org/node/3518527

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; confirm all `$_SESSION` write operations (set, unset, clear) are handled
- [ ] **Coverage** — add fixture for: `$_SESSION['key'] = $value`; `unset($_SESSION['key'])`; `$_SESSION = []`; `$_SESSION['nested']['key'] = $value`
- [ ] **Edge cases** — test: `$_SESSION['key']` read access (should NOT be changed, only writes); `$_SESSION` passed by reference; `$_SESSION` in a global scope vs inside a function

---

### ReplaceSystemPerformanceGzipKeyRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceSystemPerformanceGzipKeyRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceSystemPerformanceGzipKeyRector/`
- Drupal-digest: `replace-deprecated-system-performance-css-gzip-js-gzip-3184242.php`
- Change record: https://www.drupal.org/node/3184242

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; document gaps
- [ ] **Coverage** — add fixture pairs for all transformation variants in the change record
- [ ] **Edge cases** — test: the config key used in array access vs `get()`/`set()` method calls; nested config key patterns; unrelated config keys with similar names not touched

---

### ReplaceThemeGetSettingRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceThemeGetSettingRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceThemeGetSettingRector/`
- Drupal-digest: `replace-deprecated-theme-get-setting-and-system-default-3573896.php`
- Change record: https://www.drupal.org/node/3573896

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; confirm both `theme_get_setting()` and `_system_default_theme_features()` are handled
- [ ] **Coverage** — add fixture for `_system_default_theme_features()` replacement; fixture for various argument combinations of `theme_get_setting()`
- [ ] **Edge cases** — test: call with one argument vs two arguments (`$theme_name` parameter); result used inline vs assigned; `theme_get_setting()` with a variable key (should it still be transformed?)

---

### ReplaceUserSessionNamePropertyRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceUserSessionNamePropertyRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceUserSessionNamePropertyRector/`
- Drupal-digest: `replace-deprecated-usersession-name-property-read-with-3513856.php`
- Change record: https://www.drupal.org/node/3513856

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; document gaps
- [ ] **Coverage** — add fixture for: `$session->name` used in a string; used as a method argument; used in a comparison
- [ ] **Edge cases** — test: `->name` on an unrelated class not typed as `UserSession` (type guard check); write access `$session->name = 'value'` (should NOT be changed — only read access is deprecated); null-safe `$session?->name`

---

### ReplaceViewsProceduralFunctionsRector
- Source: `src/Drupal11/Rector/Deprecation/ReplaceViewsProceduralFunctionsRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/ReplaceViewsProceduralFunctionsRector/`
- Drupal-digest: `replace-deprecated-views-procedural-functions-with-oo-3572243.php`
- Change record: https://www.drupal.org/node/3572243

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; list all functions covered; confirm each has a fixture
- [ ] **Coverage** — add fixture pair for each function that does not yet have a dedicated fixture
- [ ] **Edge cases** — test: each function with result used in different expression positions; result used in a chain; call with different argument counts

---

### StatementPrefetchIteratorFetchColumnRector
- Source: `src/Drupal11/Rector/Deprecation/StatementPrefetchIteratorFetchColumnRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/StatementPrefetchIteratorFetchColumnRector/`
- Drupal-digest: `replace-deprecated-statementprefetchiterator-fetchcolumn-3490200.php`
- Change record: https://www.drupal.org/node/3490200

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; confirm the `$this->clientStatement` exclusion guard is correct
- [ ] **Coverage** — add fixture for: `fetchColumn()` called on `$this->clientStatement` (should NOT be renamed); `fetchColumn()` with an explicit column index argument; `fetchColumn()` with no argument
- [ ] **Edge cases** — test: `fetchColumn()` on a native `\PDO` object (should NOT be renamed); `fetchColumn()` on any other property fetch that is not `clientStatement` (should be renamed)

---

### StripMigrationDependenciesExpandArgRector
- Source: `src/Drupal11/Rector/Deprecation/StripMigrationDependenciesExpandArgRector.php`
- Test: `tests/src/Drupal11/Rector/Deprecation/StripMigrationDependenciesExpandArgRector/`
- Drupal-digest: `strip-removed-expand-argument-from-getmigrationdependencies-3574717.php`
- Change record: https://www.drupal.org/node/3574717

Tasks:
- [ ] **Analyze** — compare rector against drupal-digest source and change record; confirm the type guard on `MigrationInterface` is correct and sufficient
- [ ] **Coverage** — add fixture for: `getMigrationDependencies(FALSE)` (arg removed); `getMigrationDependencies()` with no arg (should not be changed); result used inline in a merge
- [ ] **Edge cases** — test: call on concrete class typed as `Migration` (covered by interface?); `getMigrationDependencies(TRUE)` — confirm TRUE variant also removed; call on an unrelated class with same method name is not touched

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
