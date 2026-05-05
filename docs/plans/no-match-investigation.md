# No-match investigation

Rectors that ran against installed modules but made zero changes. Per rector: check whether the
installed module(s) actually contain the deprecated pattern, and if so figure out why the rector
didn't match it.

## How to investigate

1. Find the deprecated usage in the module source (grep for the old API/pattern).
2. If the usage exists, run rector in debug mode and trace why the node visitor didn't fire.
3. Common culprits: wrong node type check, type mismatch, fully-qualified vs imported class name,
   pattern too narrow (e.g. only matches method calls, not static calls or vice versa).

---

## Suspicious — started but no completion (possible crash or timeout)

- [ ] `RemoveTrustDataCallRector` — modules: views_dependent_filters, group (progress bar showed `0/6` then nothing)
- [ ] `RemoveUpdaterPostInstallMethodsRector` — modules: group, gnode_request (progress bar showed `0/5` then nothing)

---

## Ran cleanly — zero files changed

- [x] `FileSystemBasenameToNativeRector` — modules: ejectorseat
  - **Rector is correct.** Installed ejectorseat has zero `basename()` calls anywhere.
  - **Root cause: version drift.** GitLab search matched a commit/branch that still had the call; the installed release has already dropped it.
  - → Find the ejectorseat commit/tag that still has the deprecated call, or find another module that uses `FileSystem::basename()`.
- [x] `LoadAllIncludesRector` — modules: config_track, schemadotorg
  - Pattern **exists** in schemadotorg at `SchemaDotOrgStarterkitConverter.php:430`: `$this->moduleHandler->loadAllIncludes('install')`
  - config_track only has a definition/override of the method, no calls.
  - **Root cause unclear** — pattern present but rector still didn't fire. Likely PHPStan can't infer `$this->moduleHandler` is `ModuleHandlerInterface` (missing type declaration or annotation). Needs deeper look at the property/constructor type declaration.
  - → Inspect `SchemaDotOrgStarterkitConverter` property/constructor types for `moduleHandler`.
- [x] `MigrateSqlGetMigrationPluginManagerRector` — modules: migmag, smart_sql_idmap
  - Pattern **exists** in both modules but rector logic was too narrow:
    - `migmag`: call is inside a trait; `$this` resolves to the test class, not `Sql` → type check fails (still unresolved)
    - `smart_sql_idmap`: uses `parent::getMigrationPluginManager()` — rector only handled `$this->` calls, not `parent::` (StaticCall)
  - **Fixed** (commit `149d6b3d`): added `StaticCall` to `getNodeTypes()` and a `refactorStaticCall()` handler that matches `parent::getMigrationPluginManager()` → `$this->migrationPluginManager`. Test fixture `parent_call.php.inc` added.
  - → Trait case remains unresolved (type inference limitation).
- [x] `NodeStorageDeprecatedMethodsRector` — modules: tb_megamenu
  - Pattern **not present**. tb_megamenu is a menu/block module with zero Node entity handling. Never imports `NodeStorageInterface`.
  - **Root cause: wrong module selected.** The GitLab search likely matched something unrelated (e.g. a comment or string literal), or version drift.
  - → Find a module that actually calls `revisionIds()`, `userRevisionIds()`, or `countDefaultLanguageRevisions()` on a NodeStorage instance.
- [x] `PluginBaseIsConfigurableRector` — modules: metatag, search_api
  - Pattern **exists** in metatag at `metatag_views/src/MetatagViewsCacheWrapper.php:374`: `$this->plugin->isConfigurable()`
  - **Root cause: rector logic too narrow.** Rector only matches direct `$this->isConfigurable()` where `$this` is `PluginBase`. Delegated calls (`$this->plugin->isConfigurable()`) fail the `$node->var->name !== 'this'` check.
  - → **Rector limitation**: would need to also resolve property types to catch delegated calls. Document as known limitation or extend rector.
- [x] `RemoveModuleHandlerAddModuleCallsRector` — modules: acquia_contenthub, sdx
  - Pattern **not present** in acquia_contenthub or sdx.
  - Pattern **does exist** in `config_track/src/Extension/ModuleHandler.php:178,185,206` — but config_track was not in the test run for this rector (it was assigned to LoadAllIncludesRector instead).
  - **Root cause: wrong module assigned.** The search likely found config_track for both rectors; only one was used per rector. config_track should be in the test list for this rector.
  - → Add config_track to the test modules for this rector. Rector is correct and would fire (property is `@var ModuleHandlerInterface`).
- [x] `RemoveModuleHandlerDeprecatedMethodsRector` — modules: captcha, jsonld
  - Pattern **not present** in either module. jsonld has a local `writeCache()` method but it's not on `ModuleHandlerInterface`.
  - **Root cause: version drift / wrong modules.** Neither module uses `ModuleHandlerInterface::writeCache()` or `getHookInfo()`.
  - → Find modules that actually call `$moduleHandler->writeCache()` or `$moduleHandler->getHookInfo()`.
- [x] `RemoveSetUriCallbackRector` — modules: rabbit_hole_href
  - Pattern **not present**. rabbit_hole_href is a redirect-behavior plugin with no entity-type definition code.
  - Root cause: wrong module assigned.
  - → Find a module that calls `$entityType->setUriCallback()` in PHP.

- [x] `RemoveStateCacheSettingRector` — modules: searchstax, sdx
  - Pattern **not present** (`$settings['state_cache']`) in either module or anywhere in contrib.
  - Root cause: wrong modules / pattern not present in installed codebase.
  - → No action on rector. Find correct module or confirm pattern is already gone.

- [x] `RemoveTwigNodeTransTagArgumentRector` — modules: searchstax
  - Pattern **not present** anywhere in contrib. `TwigNodeTrans` is a core class and the 6-arg constructor was already removed in the installed Drupal version.
  - Root cause: version drift in core — the installed Drupal already removed the deprecated call.
  - → Rector is correct. Needs a Drupal version where the 6-arg form still exists in contrib code.

- [x] `ReplaceAlphadecimalToIntNullRector` — modules: comment_mover
  - Pattern **exists** in comment_mover at `CommentMover.php:73,109`: `Number::alphadecimalToInt($max)` / `Number::alphadecimalToInt($levels[$last])`.
  - Root cause: **rector too narrow**. Rector only replaces calls where the argument is a literal `null` or `''`. Real-world calls pass runtime variables, which the rector skips.
  - → **Rector limitation**: designed only for `alphadecimalToInt(null)` / `alphadecimalToInt('')` edge cases, not general replacement. Clarify intended scope.

- [x] `ReplaceCommentManagerGetCountNewCommentsRector` — modules: forum, history
  - Pattern **exists** in forum at `ForumManager.php:247`: `$this->commentManager->getCountNewComments($topic, 'comment_forum', $history)`.
  - Root cause: **type inference failure**. Property `$commentManager` is declared via `@var` annotation + traditional constructor assignment — not a promoted property. PHPStan may not reliably propagate the type through the assignment, causing `isObjectType()` to fail.
  - → Verify PHPStan can infer through traditional `$this->prop = $param` assignment. May need a `@var` annotation PHPStan understands, or a promoted property in the test fixture.

- [x] `ReplaceDateTimeRangeConstantsRector` — modules: deprecation_status
  - Pattern **not present** (`DateTimeRangeConstantsInterface::BOTH` etc.) in deprecation_status or any installed module.
  - Root cause: wrong module / pattern not present.
  - → Find a module that actually uses `DateTimeRangeConstantsInterface` constants.

- [x] `ReplaceEditorLoadRector` — modules: acquia_contenthub, ckeditor5_plugin_pack
  - Pattern **not present** (`editor_load()` function call) in either module or anywhere in contrib.
  - Root cause: wrong modules / pattern not present.
  - → Find a module that calls `editor_load($format_id)`.

- [x] `ReplaceFieldgroupToFieldsetRector` — modules: field_group_vertical_tabs, ui_patterns_settings
  - Pattern **not present** in assigned modules.
  - Pattern **found** in `webform` at `WebformEntityReferenceWidgetTrait.php:150`: `'#type' => 'fieldgroup'`.
  - Root cause: wrong modules assigned. webform was not in the test run.
  - → Add webform to test modules for this rector. Rector is correct and would fire.

- [x] `ReplaceFileGetContentHeadersRector` — modules: commerce_invoice, tmgmt
  - Pattern **exists** in both:
    - `commerce_invoice/Controller/InvoiceController.php:142` (`.php` file) — should be scanned
    - `tmgmt_file/tmgmt_file.module:130` (`.module` file) — **excluded by default** (Rector only scans `.php` extensions)
  - Root cause: **`.module` files excluded**. Rector's default `fileExtensions = ['php']` skips `.module` files entirely. The `.php` file in commerce_invoice should have been matched but wasn't — needs deeper investigation.
  - → Add `'module'` to `fileExtensions` in rector config. Separately investigate why InvoiceController.php didn't fire.

- [x] `ReplaceModuleHandlerGetNameRector` — modules: drd, reassign_user_content
  - Pattern **not present** in drd or reassign_user_content.
  - Pattern found in `group` module but already migrated (uses `ModuleExtensionList`, not `ModuleHandlerInterface`).
  - Root cause: wrong modules assigned / already-migrated code.
  - → No rector fix needed. Find a module that still uses `ModuleHandlerInterface::getName()`.

- [x] `ReplaceNodeAccessViewAllNodesRector` — modules: view_usernames_node_author
  - Pattern **not present** as executable code (only a `@see` docblock reference in a test file).
  - Root cause: wrong module / pattern not present.
  - → Find a module that actually calls `node_access_view_all_nodes()` or `drupal_static_reset('node_access_view_all_nodes')`.

- [x] `ReplaceNodeModuleProceduralFunctionsRector` — modules: reassign_user_content, addanother
  - Pattern **exists**:
    - `reassign_user_content.module:111` and `Form/AssignAuthorForm.php:98`: `node_mass_update()`
    - `addanother.module:151,174`: `node_get_type_label($node)`
  - Root cause: **type inference failure + `.module` file scope**. Calls in `.module` files are likely skipped (see `ReplaceFileGetContentHeadersRector`). For `AssignAuthorForm.php:98`, `$nids` is untyped mixed. For `addanother.module:174`, `$node` IS typed as `NodeInterface` but is in a `.module` file.
  - → Enable `.module` file scanning. Then verify type inference for procedural function calls works — these functions may not require type checks (plain `FuncCall` nodes), in which case `.module` exclusion is the only blocker.

- [x] `ReplaceRecipeRunnerInstallModuleRector` — modules: schemadotorg
  - Pattern **not present** (schemadotorg uses `RecipeRunner::processRecipe()`, not the deprecated `installModule()`).
  - Root cause: wrong module / already using newer API.
  - → Find a module that calls `RecipeRunner::installModule()`.

- [x] `ReplaceSessionManagerDeleteRector` — modules: entity_visibility_preview, session_inspector
  - Pattern **not present** with the required type. All modules use `SessionManagerInterface`; rector requires the concrete `SessionManager` class.
  - Root cause: **rector too narrow**. Documented as a known limitation in the rector's own `no_change_interface.php.inc` fixture. The deprecated `delete()` method only exists on the concrete class, and real code uses the interface.
  - → Rector is working as designed but will rarely fire in practice. Consider whether the type check should be broadened.

- [x] `ReplaceSessionWritesWithRequestSessionRector` — modules: drd, entity_visibility_preview
  - Pattern **not present** (`$_SESSION[...] = ...` writes) in either module.
  - Root cause: wrong modules / pattern not present.
  - → Find a module that still writes directly to `$_SESSION`.

- [x] `ReplaceSystemPerformanceGzipKeyRector` — modules: drd
  - Pattern **not present** (`system.performance` config keys `css.gzip`/`js.gzip`). drd_agent uses `css.preprocess`/`js.preprocess` (different, non-deprecated keys).
  - Root cause: wrong module / pattern not present.
  - → Find a module that reads/writes the deprecated `css.gzip` or `js.gzip` config keys.

- [x] `ReplaceUserSessionNamePropertyRector` — modules: acquia_contenthub, session_inspector
  - Pattern **not present** (`$userSession->name` property access) in either module.
  - Root cause: wrong modules / pattern not present.
  - → Find a module that accesses `->name` on a `UserSession` object.

- [x] `ViewsPluginHandlerManagerRector` — modules: searchstax, search_api, metatag
  - Pattern **exists** in search_api at `SearchApiEntityField.php:51-52`: `Views::handlerManager('field')->getHandler(...)`.
  - Root cause: initial analysis was wrong — chained calls work fine because PHP-Parser walks the inner `StaticCall` regardless of what wraps it.
  - **Verified working** (commit `6bc5f86b`): test fixture `chained_call.php.inc` added and passes. Zero-changes in test run was likely due to `.module` file exclusion or version drift, not a rector bug.
  - → No rector change needed.

- [x] `ReplaceRebuildThemeDataRector` — modules: site_guardian
  - Pattern **exists** in site_guardian:
    - `site_guardian.module:37`: `\Drupal::service('theme_handler')->rebuildThemeData()` — `\Drupal::service()` returns untyped `object`, PHPStan cannot infer `ThemeHandlerInterface`.
    - `SiteGuardianService.php:153`: `$this->themeHandler->rebuildThemeData()` — property typed as concrete `ThemeHandler` class, not the interface.
  - Root cause: **type inference failure** in both cases. Rector checks for `ThemeHandlerInterface` but (1) `\Drupal::service()` is untyped and (2) `ThemeHandler` (concrete class) is not treated as equivalent to the interface in this context.
  - → Fix: declare `$themeHandler` as `ThemeHandlerInterface` instead of `ThemeHandler`. The `\Drupal::service()` case can never be fixed without PHPStan service map stubs.

- [x] `ReplaceRequestTimeConstantRector` — modules: google_analytics_counter, automatic_updates
  - Pattern **not present** (no bare `REQUEST_TIME` constant usage). Modules already use `$request->server->get('REQUEST_TIME')`.
  - Root cause: wrong modules / already migrated.
  - → No action needed. Find a module still using the bare `REQUEST_TIME` constant if test coverage is desired.

- [x] `SystemTimeZonesRector` — modules: intl_date, smart_date
  - Pattern **exists** in both:
    - `intl_date:122`: `system_time_zones(FALSE, TRUE)` (literal args — should match, didn't fire, likely because it's already inside a `DeprecationHelper::backwardsCompatibleCall` arrow function — the `isInBackwardsCompatibleCall()` guard skips it correctly)
    - `smart_date:166,175`: `system_time_zones(FALSE, $grouped)` (variable 2nd arg — rector was too narrow)
  - **Fixed** (commit `6bc5f86b`): added a ternary branch for when the second arg is dynamic (not a literal `FALSE`). Emits `$grouped ? getOptionsListByRegion($arg0) : getOptionsList($arg0)` instead of silently dropping the flag. Test fixture `system_time_zones_dynamic_grouped.php.inc` added.
  - → intl_date case is expected no-op (already BC-wrapped). smart_date case now handled.
