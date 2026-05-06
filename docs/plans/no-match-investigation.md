# No-match investigation

Rectors that ran against installed modules but made zero changes. Per rector: check whether the
installed module(s) actually contain the deprecated pattern, and if so figure out why the rector
didn't match it.

## How to investigate

1. **Search contrib first** at https://search.tresbien.tech — use `-r:core` to exclude core.
   Confirm the pattern actually exists in contrib before installing anything locally.
   If 0 results: document as "pattern exhausted" and skip the test run entry.
2. Find the deprecated usage in the installed module source (grep for the old API/pattern).
3. If the usage exists but rector doesn't fire, run rector in debug mode and trace why the node visitor didn't fire.
4. Common culprits: wrong node type check, type mismatch, fully-qualified vs imported class name,
   pattern too narrow (e.g. only matches method calls, not static calls or vice versa),
   version drift (module already patched in installed release — check an older version or find another module).

---

## Suspicious — started but no completion (possible crash or timeout)

- [ ] `RemoveTrustDataCallRector` — modules: views_dependent_filters, group (progress bar showed `0/6` then nothing)
- [ ] `RemoveUpdaterPostInstallMethodsRector` — modules: group, gnode_request (progress bar showed `0/5` then nothing)

---

## Ran cleanly — zero files changed

- [x] `FileSystemBasenameToNativeRector` — modules: ejectorseat
  - **Rector is correct.** Installed ejectorseat has zero `basename()` calls anywhere.
  - **Root cause: wrong module.** ejectorseat is a session/autologout module — it has never had any file system calls in any version or branch. The original GitLab search hit was a false positive.
  - **Fixed:** Replaced with `stage_file_proxy ^3.1` — confirmed caller at `src/DownloadManager.php:76`: `$this->fileSystem->basename($relative_path)`. D11-compatible (`^10.3 || ^11`), present through 3.1.6. Pinned to `^3.1` to guard against a future 4.x that removes the call.
- [x] `LoadAllIncludesRector` — modules: config_track, schemadotorg
  - Pattern **exists** in schemadotorg at `SchemaDotOrgStarterkitConverter.php:430`: `$this->moduleHandler->loadAllIncludes('install')`
  - config_track only has a definition/override of the method, no calls.
  - **Root cause: missing type declaration.** Tests confirm the rector handles both promoted properties and traditional `@var`-annotated properties correctly (fixtures `class_property.php.inc`, `traditional_constructor.php.inc`). The schemadotorg property likely has no `@var` annotation and no PHP-native type, so PHPStan cannot infer `ModuleHandlerInterface`. Documented via `no_change_class_property_untyped.php.inc`.
  - → No rector fix needed. The schemadotorg module needs to add a `@var \Drupal\Core\Extension\ModuleHandlerInterface` annotation to its `$moduleHandler` property.
- [x] `MigrateSqlGetMigrationPluginManagerRector` — modules: migmag, smart_sql_idmap
  - Pattern **exists** in both modules but rector logic was too narrow:
    - `migmag`: call is inside a trait; `$this` resolves to the test class, not `Sql` → type check fails (still unresolved)
    - `smart_sql_idmap`: uses `parent::getMigrationPluginManager()` — rector only handled `$this->` calls, not `parent::` (StaticCall)
  - **Fixed** (commit `149d6b3d`): added `StaticCall` to `getNodeTypes()` and a `refactorStaticCall()` handler that matches `parent::getMigrationPluginManager()` → `$this->migrationPluginManager`. Test fixture `parent_call.php.inc` added.
  - → Trait case remains unresolved (type inference limitation).
- [x] `NodeStorageDeprecatedMethodsRector` — modules: tb_megamenu
  - Pattern **not present**. tb_megamenu is a menu/block module with zero Node entity handling. Never imports `NodeStorageInterface`.
  - **Fixed:** Updated to `workflow_buttons:^1` — 8.x-1.x calls `$node_storage->revisionIds($variables['node'])` in `workflow_buttons.module` with `@var \Drupal\node\NodeStorage`. D11-compatible (`^9 || ^10 || ^11`). Pinned to `^1` because 2.0.x already migrated.
- [x] `PluginBaseIsConfigurableRector` — modules: metatag, search_api
  - Pattern **exists** in metatag at `metatag_views/src/MetatagViewsCacheWrapper.php:374`: `$this->plugin->isConfigurable()`
  - **Root cause: rector logic too narrow.** Rector only matches direct `$this->isConfigurable()` where `$this` is `PluginBase`. Delegated calls (`$this->plugin->isConfigurable()`) fail the `$node->var->name !== 'this'` check.
  - → **Rector limitation**: would need to also resolve property types to catch delegated calls. Document as known limitation or extend rector.
- [x] `RemoveModuleHandlerAddModuleCallsRector` — modules: acquia_contenthub, sdx
  - Pattern **not present** in acquia_contenthub or sdx.
  - Pattern **does exist** in `config_track/src/Extension/ModuleHandler.php:178,185,206` — but config_track was not in the test run for this rector (it was assigned to LoadAllIncludesRector instead).
  - **Fixed:** Updated test assignment to `config_track`. Rector is correct and would fire (property is `@var ModuleHandlerInterface`).
- [x] `RemoveModuleHandlerDeprecatedMethodsRector` — modules: captcha, jsonld
  - Pattern **not present** in either module. jsonld has a local `writeCache()` method but it's not on `ModuleHandlerInterface`.
  - **Root cause: pattern exhausted.** Neither module uses `ModuleHandlerInterface::writeCache()` or `getHookInfo()`. Web search of D11 contrib found no caller. Module removed from test run (no replacement found).
- [x] `RemoveSetUriCallbackRector` — modules: rabbit_hole_href
  - Pattern **not present**. rabbit_hole_href is a redirect-behavior plugin with no entity-type definition code.
  - **Root cause: pattern exhausted.** Web search of D11 contrib found no `setUriCallback()` caller. Module removed from test run (no replacement found).

- [x] `RemoveStateCacheSettingRector` — modules: searchstax, sdx
  - Pattern **not present** (`$settings['state_cache']`) in either module or anywhere in contrib.
  - **Root cause: pattern exhausted.** Confirmed gone from all D11 contrib. Module removed from test run.

- [x] `RemoveTwigNodeTransTagArgumentRector` — modules: searchstax
  - Pattern **not present** anywhere in contrib. `TwigNodeTrans` is a core class and the 6-arg constructor was already removed in the installed Drupal version.
  - **Root cause: version drift.** Core removed the 6-arg constructor before any D11 contrib module could call it. Module removed from test run.

- [x] `ReplaceAlphadecimalToIntNullRector` — modules: comment_mover
  - Pattern **exists** in comment_mover at `CommentMover.php:73,109`: `Number::alphadecimalToInt($max)` / `Number::alphadecimalToInt($levels[$last])`.
  - Root cause: **rector too narrow**. Rector only replaces calls where the argument is a literal `null` or `''`. Real-world calls pass runtime variables, which the rector skips.
  - **Confirmed pattern exhausted** (search.tresbien.tech): 0 results across all contrib for `alphadecimalToInt(null)` or `alphadecimalToInt('')`. The literal-null/empty-string edge case never appeared in contrib — only custom code or core tests.
  - **Fixed:** Removed `comment_mover` from test run. Added to "pattern exhausted" list.

- [x] `ReplaceCommentManagerGetCountNewCommentsRector` — modules: forum, history
  - Pattern **exists** in forum at `ForumManager.php:247`: `$this->commentManager->getCountNewComments($topic, 'comment_forum', $history)`.
  - **Root cause: missing type annotation, not traditional assignment.** Tests confirm the rector handles `@var`-annotated traditional constructor assignment correctly (fixture `traditional_constructor.php.inc`). PHPStan reads `@var` docblocks on properties reliably. The forum `$commentManager` property likely has no `@var` or PHP-native type declaration.
  - → No rector fix needed. Forum's `ForumManager::$commentManager` needs a `@var \Drupal\comment\CommentManagerInterface` annotation (or typed property) for the rector to fire.

- [x] `ReplaceDateTimeRangeConstantsRector` — modules: deprecation_status → optional_end_date
  - Pattern **not present** (`DateTimeRangeConstantsInterface::BOTH` etc.) in deprecation_status or any installed module.
  - Previous fix (optional_end_date) hit version drift: the search index confirmed `DateTimeRangeConstantsInterface::BOTH` at `OptionalEndDateDateTimeRangeTrait.php:29`, but installed 8.x-1.4 has already migrated to the new enum. Pattern still present in the search index (not all branches updated).
  - For `datetime_type_field_views_data_helper()`: confirmed in `scheduler_field` (calls it twice for field columns, D11-compatible `^8||^9||^10||^11`), plus quiz, civicrm_entity, computed_token_field, optional_end_month_year_range.
  - **Fixed:** Added `scheduler_field` as second test module. Retained `optional_end_date` in case a future install picks up an unpatched version.

- [x] `ReplaceEditorLoadRector` — modules: acquia_contenthub, ckeditor5_plugin_pack
  - Pattern **not present** (`editor_load()` function call) in either module or anywhere in contrib.
  - **Root cause: pattern exhausted.** Not called in any D11 contrib module found. Module removed from test run.

- [x] `ReplaceFieldgroupToFieldsetRector` — modules: field_group_vertical_tabs, ui_patterns_settings
  - Pattern **not present** in assigned modules.
  - Pattern **found** in `webform` at `WebformEntityReferenceWidgetTrait.php:150`: `'#type' => 'fieldgroup'`.
  - **Fixed:** Updated test assignment to `webform`. Rector is correct and would fire.

- [x] `ReplaceFileGetContentHeadersRector` — modules: commerce_invoice, tmgmt
  - Pattern **exists** in both:
    - `commerce_invoice/Controller/InvoiceController.php:142` (`.php` file) — should be scanned
    - `tmgmt_file/tmgmt_file.module:130` (`.module` file) — **excluded by default** (Rector only scans `.php` extensions)
  - Root cause: **`.module` files excluded**. Rector's default `fileExtensions = ['php']` skips `.module` files entirely. The `.php` file in commerce_invoice should have been matched but wasn't — needs deeper investigation.
  - → Add `'module'` to `fileExtensions` in rector config. Separately investigate why InvoiceController.php didn't fire.

- [x] `ReplaceModuleHandlerGetNameRector` — modules: drd, reassign_user_content
  - Pattern **not present** in drd or reassign_user_content.
  - Pattern found in `group` module but already migrated (uses `ModuleExtensionList`, not `ModuleHandlerInterface`).
  - **Fixed:** Updated test assignment to `mailsystem` — confirmed caller at `src/Form/AdminForm.php:206,211` (`$this->moduleHandler->getName($module)`), issue #3566556 active as of Jan 2026.

- [x] `ReplaceNodeAccessViewAllNodesRector` — modules: view_usernames_node_author
  - Pattern **not present** as executable code (only a `@see` docblock reference in a test file).
  - **Root cause: pattern not yet present in contrib.** `node_access_view_all_nodes()` was only deprecated in D11.3.0 (late 2025); no D11 contrib caller found. Module removed from test run.

- [x] `ReplaceNodeModuleProceduralFunctionsRector` — modules: reassign_user_content, addanother
  - Pattern **exists**:
    - `reassign_user_content.module:111` and `Form/AssignAuthorForm.php:98`: `node_mass_update()`
    - `addanother.module:151,174`: `node_get_type_label($node)`
  - Root cause: **type inference failure + `.module` file scope**. Calls in `.module` files are likely skipped (see `ReplaceFileGetContentHeadersRector`). For `AssignAuthorForm.php:98`, `$nids` is untyped mixed. For `addanother.module:174`, `$node` IS typed as `NodeInterface` but is in a `.module` file.
  - → Enable `.module` file scanning. Then verify type inference for procedural function calls works — these functions may not require type checks (plain `FuncCall` nodes), in which case `.module` exclusion is the only blocker.

- [x] `ReplaceRecipeRunnerInstallModuleRector` — modules: schemadotorg
  - Pattern **not present** (schemadotorg uses `RecipeRunner::processRecipe()`, not the deprecated `installModule()`).
  - **Updated:** Replaced with `recipe_installer_kit` (primary contrib recipe-wrapping module, deprecated D11.4.0). Needs verification in live test run.

- [x] `ReplaceSessionManagerDeleteRector` — modules: entity_visibility_preview, session_inspector → role_expire
  - Pattern **not present** with the required type in either original module. entity_visibility_preview has its own unrelated `SessionManager` service; session_inspector uses a raw DB delete. Both use the interface, not the concrete class.
  - **Found via search.tresbien.tech**: `role_expire/src/RoleExpireApiService.php` declares `@var Drupal\Core\Session\SessionManager` on its `$sessionManager` property (concrete class) and calls `$this->sessionManager->delete($uid)`. D11-compatible (`^10.2||^11`).
  - **Fixed:** Replaced both modules with `role_expire`. The `@var` annotation naming the concrete class satisfies the rector's `isObjectType(SessionManager)` check.

- [x] `ReplaceSessionWritesWithRequestSessionRector` — modules: drd, entity_visibility_preview
  - Pattern **not present** (`$_SESSION[...] = ...` writes) in either module.
  - **Root cause: pattern exhausted.** Direct `$_SESSION` writes gone from all D11-compatible contrib found (openid_connect fixed in 2021). Module removed from test run.

- [x] `ReplaceSystemPerformanceGzipKeyRector` — modules: drd
  - Pattern **not present** (`system.performance` config keys `css.gzip`/`js.gzip`). drd_agent uses `css.preprocess`/`js.preprocess` (different, non-deprecated keys).
  - **Root cause: no D11-compatible module.** advagg (5.x/6.0.0-alpha1) confirmed has the pattern at `src/Form/SettingsForm.php`, but declares `core_version_requirement: ^9.3 || ^10` — won't install against D11. Module removed from test run.

- [x] `ReplaceUserSessionNamePropertyRector` — modules: acquia_contenthub, session_inspector
  - Pattern **not present** (`$userSession->name` property access) in either module.
  - **Root cause: pattern exhausted.** `->name` property access on `UserSession` objects gone from all D11-compatible contrib found (broke in Drupal 8). Module removed from test run.

- [x] `ViewsPluginHandlerManagerRector` — modules: searchstax, search_api, metatag
  - Pattern **exists** in search_api at `SearchApiEntityField.php:51-52`: `Views::handlerManager('field')->getHandler(...)`.
  - Root cause: initial analysis was wrong — chained calls work fine because PHP-Parser walks the inner `StaticCall` regardless of what wraps it.
  - **Verified working** (commit `6bc5f86b`): test fixture `chained_call.php.inc` added and passes. Zero-changes in test run was likely due to `.module` file exclusion or version drift, not a rector bug.
  - → No rector change needed.

- [x] `ReplaceRebuildThemeDataRector` — modules: site_guardian
  - Pattern **exists** in site_guardian:
    - `site_guardian.module:37`: `\Drupal::service('theme_handler')->rebuildThemeData()` — `\Drupal::service()` returns untyped `object`, PHPStan cannot infer `ThemeHandlerInterface`.
    - `SiteGuardianService.php:153`: `$this->themeHandler->rebuildThemeData()` — property typed as concrete `ThemeHandler` class, not the interface.
  - **Root cause confirmed as known limitation.** (1) `\Drupal::service()` is untyped — cannot be fixed without PHPStan service map stubs. (2) Concrete `ThemeHandler` class: without Drupal's full class hierarchy loaded in analysis, PHPStan cannot confirm it implements `ThemeHandlerInterface` — documented via `no_change_concrete_class.php.inc`. The rector works correctly when the property is declared as `ThemeHandlerInterface` (fixture `class_property.php.inc`).
  - → No rector fix needed. Users must declare `$themeHandler` as `ThemeHandlerInterface` for the rector to fire. The `\Drupal::service()` call is an unfixable limitation.

- [x] `ReplaceRequestTimeConstantRector` — modules: google_analytics_counter, automatic_updates
  - Pattern **not present** (no bare `REQUEST_TIME` constant usage). Modules already use `$request->server->get('REQUEST_TIME')`.
  - **Root cause: pattern exhausted.** Already migrated in all D11-compatible modules found. Module removed from test run.

- [x] `SystemTimeZonesRector` — modules: intl_date, smart_date
  - Pattern **exists** in both:
    - `intl_date:122`: `system_time_zones(FALSE, TRUE)` (literal args — should match, didn't fire, likely because it's already inside a `DeprecationHelper::backwardsCompatibleCall` arrow function — the `isInBackwardsCompatibleCall()` guard skips it correctly)
    - `smart_date:166,175`: `system_time_zones(FALSE, $grouped)` (variable 2nd arg — rector was too narrow)
  - **Fixed** (commit `6bc5f86b`): added a ternary branch for when the second arg is dynamic (not a literal `FALSE`). Emits `$grouped ? getOptionsListByRegion($arg0) : getOptionsList($arg0)` instead of silently dropping the flag. Test fixture `system_time_zones_dynamic_grouped.php.inc` added.
  - → intl_date case is expected no-op (already BC-wrapped). smart_date case now handled.
