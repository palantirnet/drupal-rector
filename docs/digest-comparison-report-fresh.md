# Digest vs Rector Comparison Report (Fresh)

Generated: 2026-05-02
Branch: feature/digest-rectors
Digest source: ~/projects/drupal-digest-fresh/rector/

---

## Overview Table

| Rector | Issue | Digest File | Rector File | Notable Changes | Status |
|---|---|---|---|---|---|
| ReplaceModuleHandlerGetNameRector | [#3571063](https://drupal.org/i/3571063) | `rules/replace-removed-modulehandlerinterface-getname-with-3571063.php` | `src/Drupal10/Rector/Deprecation/ReplaceModuleHandlerGetNameRector.php` | Integrated into drupal-rector framework (AbstractDrupalCoreRector, DrupalIntroducedVersionConfiguration, ConfiguredCodeSample); namespace added; class name preserved | Substantial |
| ReplaceRebuildThemeDataRector | [#3571068](https://drupal.org/i/3571068) | `rules/replace-removed-themehandlerinterface-rebuildthemedata-with-3571068.php` | `src/Drupal10/Rector/Deprecation/ReplaceRebuildThemeDataRector.php` | Integrated into framework (AbstractDrupalCoreRector, versioned configuration); namespace added; type check added; class name preserved | Substantial |
| ReplaceRequestTimeConstantRector | [#3395986](https://drupal.org/i/3395986) | `rules/add-timeinterface-time-argument-to-plugin-constructor-3395986.php` | `src/Drupal10/Rector/Deprecation/ReplaceRequestTimeConstantRector.php` | **Completely different rule**: digest adds `?TimeInterface $time` constructor arguments to plugin subclasses; rector replaces `REQUEST_TIME` constant with `\Drupal::time()->getRequestTime()`. Same issue ID, entirely different transformation. | Substantial |
| ErrorCurrentErrorHandlerRector | [#3526515](https://drupal.org/i/3526515) | `rules/replace-error-currenterrorhandler-with-get-error-handler-3526515.php` | `src/Drupal11/Rector/Deprecation/ErrorCurrentErrorHandlerRector.php` | Namespace added; type check uses `ObjectType` import (rector) vs inline `new \PHPStan\Type\ObjectType` (digest); `@param StaticCall $node` doc comment dropped; logic identical | Minor/Style-only |
| FileSystemBasenameToNativeRector | [#3530461](https://drupal.org/i/3530461) | `rules/replace-filesysteminterface-basename-with-native-basename-3530461.php` | `src/Drupal11/Rector/Deprecation/FileSystemBasenameToNativeRector.php` | Namespace added; type-check strategy changed: rector uses `$this->isObjectType()` (both classes checked in loop) whereas digest calls `$callerType->isSuperTypeOf()` (the PHPStan direction differs); logic semantically equivalent | Minor/Style-only |
| LoadAllIncludesRector | [#3536431](https://drupal.org/i/3536431) | `rules/replace-deprecated-modulehandler-loadallincludes-with-3536431.php` | `src/Drupal11/Rector/Deprecation/LoadAllIncludesRector.php` | Namespace added; rector adds `ModuleHandlerInterface` type guard (digest omits it); minor import cleanup | Substantial |
| MigrateSqlGetMigrationPluginManagerRector | [#3439369](https://drupal.org/i/3439369) | `rules/replace-deprecated-sql-getmigrationpluginmanager-with-3439369.php` | `src/Drupal11/Rector/Deprecation/MigrateSqlGetMigrationPluginManagerRector.php` | Namespace added; rector uses `isObjectType(new ObjectType('Drupal\migrate\Plugin\migrate\id_map\Sql'))` to whitelist; digest uses an explicit exclusion of `Migration::getMigrationPluginManager()` instead. Logic direction inverted but functionally similar. | Substantial |
| NodeStorageDeprecatedMethodsRector | [#3396062](https://drupal.org/i/3396062) | `rules/replace-deprecated-nodestorage-revisionids-and-3396062.php` | `src/Drupal11/Rector/Deprecation/NodeStorageDeprecatedMethodsRector.php` | Namespace added; rector adds `countDefaultLanguageRevisions` removal (removes the whole statement via `NodeVisitor::REMOVE_NODE`); digest does not handle `countDefaultLanguageRevisions`; rector also registers `Expression::class` in `getNodeTypes()` for this removal | Substantial |
| PluginBaseIsConfigurableRector | [#3459533](https://drupal.org/i/3459533) | `rules/replace-deprecated-pluginbase-isconfigurable-with-3459533.php` | `src/Drupal11/Rector/Deprecation/PluginBaseIsConfigurableRector.php` | Namespace added; rector adds a `PluginBase` `ObjectType` check (not in digest); digest targets `$this->isConfigurable()` by restricting to `Variable` + name=`this` only (no type check), rector adds both that restriction AND a `PluginBase` type guard | Substantial |
| RemoveAutomatedCronSubmitHandlerRector | [#3566768](https://drupal.org/i/3566768) | `rules/remove-deprecated-automated-cron-settings-submit-calls-and-3566768.php` | `src/Drupal11/Rector/Deprecation/RemoveAutomatedCronSubmitHandlerRector.php` | Namespace added; class renamed from `RemoveAutomatedCronSettingsSubmitHandlerRector` to `RemoveAutomatedCronSubmitHandlerRector`; rector also drops the `Rector\Removing\Rector\FuncCall\RemoveFuncCallRector` dependency in the digest (digest relied on a second built-in rector for direct function calls; rector omits that); logic for `$form['#submit'][]` removal is identical | Substantial |
| RemoveCacheExpireOverrideRector | [#3576556](https://drupal.org/i/3576556) | `rules/remove-deprecated-cacheexpire-overrides-from-views-3576556.php` | `src/Drupal11/Rector/Deprecation/RemoveCacheExpireOverrideRector.php` | Namespace added; rector adds a `PARENT_FQCNS` constant and `isObjectType` fallback for deeper class hierarchies; digest uses only string matching plus a weaker `isSuperTypeOf` check; rector's isCachePluginBaseSubclass also catches `None` in its short-name list | Substantial |
| RemoveConfigSaveTrustedDataArgRector | [#3347842](https://drupal.org/i/3347842) | `rules/remove-deprecated-trusted-data-concept-from-drupal-config-3347842.php` | `src/Drupal11/Rector/Deprecation/RemoveConfigSaveTrustedDataArgRector.php` | **Split from digest**: digest handles both patterns (save arg removal + trustData chain) in one class; rector splits into two separate classes. `RemoveConfigSaveTrustedDataArgRector` handles only `Config::save(TRUE/FALSE)` pattern; adds `Config` ObjectType check (missing from digest) | Substantial |
| RemoveHandlerBaseDefineExtraOptionsRector | [#3485084](https://drupal.org/i/3485084) | `rules/remove-overrides-of-deprecated-handlerbase-3485084.php` | `src/Drupal11/Rector/Deprecation/RemoveHandlerBaseDefineExtraOptionsRector.php` | Namespace added; class renamed from `RemoveDefineExtraOptionsOverrideRector`; rector adds a `PARENT_SHORT_NAMES` constant with additional handler subclasses; rector adds `isObjectType` fallback; digest guards against modifying `HandlerBase` itself, rector uses a different approach (checking FQCNs/short names instead) | Substantial |
| RemoveLinkWidgetValidateTitleElementRector | [#3093118](https://drupal.org/i/3093118) | `rules/remove-deprecated-linkwidget-validatetitleelement-calls-3093118.php` | `src/Drupal11/Rector/Deprecation/RemoveLinkWidgetValidateTitleElementRector.php` | Namespace added; class name preserved; no logic changes; minor wording differences in `getRuleDefinition()` | Minor/Style-only |
| RemoveModuleHandlerAddModuleCallsRector | [#3528899](https://drupal.org/i/3528899) | `rules/remove-deprecated-modulehandlerinterface-addmodule-and-3528899.php` | `src/Drupal11/Rector/Deprecation/RemoveModuleHandlerAddModuleCallsRector.php` | Namespace added; rector adds `ModuleHandler` (concrete class) to the ObjectType check in addition to `ModuleHandlerInterface` (digest checks only the interface); return type hint changed from `?int` to `mixed` | Substantial |
| RemoveModuleHandlerDeprecatedMethodsRector | [#3442009](https://drupal.org/i/3442009) | `rules/remove-deprecated-modulehandlerinterface-writecache-and-3442009.php` | `src/Drupal11/Rector/Deprecation/RemoveModuleHandlerDeprecatedMethodsRector.php` | Namespace added; rector also removes standalone `getHookInfo()` expression statements (digest leaves them as bare `[]` expressions); rector refactors private helper method to improve readability | Substantial |
| RemoveRootFromConvertDbUrlRector | [#3522513](https://drupal.org/i/3522513) | `rules/remove-deprecated-string-root-from-database-3522513.php` | `src/Drupal11/Rector/Deprecation/RemoveRootFromConvertDbUrlRector.php` | Namespace added; class renamed from `RemoveRootFromConvertDbUrlToConnectionInfoRector`; rector adds `StaticPropertyFetch` and `MethodCall` to recognized second-argument types (digest does not); logic largely equivalent | Substantial |
| RemoveSetUriCallbackRector | [#2667040](https://drupal.org/i/2667040) | `rules/remove-deprecated-entitytypeinterface-seturicallback-calls-2667040.php` | `src/Drupal11/Rector/Deprecation/RemoveSetUriCallbackRector.php` | Namespace added; rector adds `EntityTypeInterface` ObjectType check (digest has none); class name preserved | Substantial |
| RemoveStateCacheSettingRector | [#3436954](https://drupal.org/i/3436954) | `rules/remove-deprecated-settings-state-cache-assignment-3436954.php` | `src/Drupal11/Rector/Deprecation/RemoveStateCacheSettingRector.php` | Namespace added; class name preserved; logic identical | Minor/Style-only |
| RemoveTrustDataCallRector | [#3347842](https://drupal.org/i/3347842) | `rules/remove-deprecated-trusted-data-concept-from-drupal-config-3347842.php` | `src/Drupal11/Rector/Deprecation/RemoveTrustDataCallRector.php` | **Split from digest**: rector splits second pattern (trustData chain removal) into its own class; adds `ConfigEntityInterface` ObjectType check (missing from digest's combined class) | Substantial |
| RemoveTwigNodeTransTagArgumentRector | [#3473440](https://drupal.org/i/3473440) | `rules/remove-deprecated-tag-argument-from-twignodetrans-3473440.php` | `src/Drupal11/Rector/Deprecation/RemoveTwigNodeTransTagArgumentRector.php` | Namespace added; class renamed from `RemoveTwigNodeTransTagArgRector`; rector checks 6 args exactly and uses `array_pop`; digest uses `array_splice($node->args, 5)` (removes from index 5 onward, potentially handles more args); rector also matches short class name `TwigNodeTrans` in addition to FQCN | Substantial |
| RemoveUpdaterPostInstallMethodsRector | [#3417136](https://drupal.org/i/3417136) | `rules/remove-deprecated-updater-postinstall-postinstalltasks-3417136.php` | `src/Drupal11/Rector/Deprecation/RemoveUpdaterPostInstallMethodsRector.php` | Namespace added; class name preserved; rector uses `toString()` for parent name comparison (digest uses same approach); UPDATER_BASE_CLASSES constant uses unescaped backslash strings (rector) vs double-escaped (digest); logic identical | Minor/Style-only |
| RemoveViewsRowCacheKeysRector | [#3564958](https://drupal.org/i/3564958) | `rules/remove-deprecated-cachepluginbase-getrowcachekeys-and-3564937.php` (issue 3564937 in filename, covers same deprecation) | `src/Drupal11/Rector/Deprecation/RemoveViewsRowCacheKeysRector.php` | Namespace added; rector adds `CachePluginBase` ObjectType check on the MethodCall; digest has no type guard, matches any `getRowCacheKeys`/`getRowId` call. Rector is more conservative/accurate. | Substantial |
| RenameStopProceduralHookScanRector | [#3495943](https://drupal.org/i/3495943) | `rules/rename-stopproceduralhookscan-attribute-to-3495943.php` | `src/Drupal11/Rector/Deprecation/RenameStopProceduralHookScanRector.php` | **Completely different implementation**: digest is a config-only snippet using built-in `RenameClassRector`; rector implements a custom rule handling both `UseUse` and `Attribute` AST nodes directly for precise class/use-statement renaming | Substantial |
| ReplaceAlphadecimalToIntNullRector | [#3442810](https://drupal.org/i/3442810) | `rules/replace-deprecated-number-alphadecimaltoint-null-calls-with-3442810.php` | `src/Drupal11/Rector/Deprecation/ReplaceAlphadecimalToIntNullRector.php` | Namespace added; class renamed from `AlphadecimalToIntNullOrEmptyRector`; logic identical; minor import style differences | Minor/Style-only |
| ReplaceCommentManagerGetCountNewCommentsRector | [#3551729](https://drupal.org/i/3551729) | `rules/replace-deprecated-commentmanagerinterface-3543035.php` (issue 3543035 in filename — closest match; issue 3551729 not found as separate file) | `src/Drupal11/Rector/Deprecation/ReplaceCommentManagerGetCountNewCommentsRector.php` | Rector integrates into AbstractDrupalCoreRector framework with versioned configuration; digest is a standalone AbstractRector; logic nearly identical but rector wraps in DrupalIntroducedVersionConfiguration | Substantial |
| ReplaceCommentUriRector | [#2010202](https://drupal.org/i/2010202) | `rules/replace-deprecated-comment-uri-with-comment-permalink-2010202.php` | `src/Drupal11/Rector/Deprecation/ReplaceCommentUriRector.php` | Namespace added; class renamed from `CommentUriToPermalinkRector`; rector allows `< 1` args (digest requires exactly 1); minor logic difference | Minor/Style-only |
| ReplaceDateTimeRangeConstantsRector | [#3574901](https://drupal.org/i/3574901) | `rules/replace-removed-datetimerangeconstantsinterface-constants-3574901.php` | `src/Drupal11/Rector/Deprecation/ReplaceDateTimeRangeConstantsRector.php` | Namespace added; class renamed from `ReplaceDatetimeDeprecatedApisRector`; logic identical; CONST_MAP indentation difference | Minor/Style-only |
| ReplaceEditorLoadRector | [#3447794](https://drupal.org/i/3447794) | `rules/replace-deprecated-editor-load-with-entity-storage-load-3447794.php` | `src/Drupal11/Rector/Deprecation/ReplaceEditorLoadRector.php` | Namespace added; class renamed from `EditorLoadDeprecationRector`; rector uses `$this->nodeFactory` helper methods for cleaner AST construction; digest does inline construction; rector checks `count($node->args) !== 1` while digest does not have arg count guard; logic equivalent | Substantial |
| ReplaceEntityOriginalPropertyRector | [#3571065](https://drupal.org/i/3571065) | `rules/replace-deprecated-entity-original-magic-property-with-3571065.php` | `src/Drupal11/Rector/Deprecation/ReplaceEntityOriginalPropertyRector.php` | Namespace added; class renamed from `EntityOriginalPropertyToMethodRector`; rector handles `NullsafePropertyFetch` (nullsafe `?->` operator) — digest omits this; rector adds `EntityInterface` type guard on `PropertyFetch`; rector also registers `NullsafePropertyFetch` in `getNodeTypes()` | Substantial |
| ReplaceEntityReferenceRecursiveLimitRector | [#3316878](https://drupal.org/i/3316878) | `rules/replace-deprecated-entityreferenceentityformatter-recursive-2940605.php` (different issue # 2940605 in filename, same pattern) | `src/Drupal11/Rector/Deprecation/ReplaceEntityReferenceRecursiveLimitRector.php` | Namespace added; class name preserved; logic identical; both replace `RECURSIVE_RENDER_LIMIT` with `20` | Minor/Style-only |
| ReplaceFieldgroupToFieldsetRector | [#3512254](https://drupal.org/i/3512254) | `rules/replace-deprecated-type-fieldgroup-with-type-fieldset-3512254.php` | `src/Drupal11/Rector/Deprecation/ReplaceFieldgroupToFieldsetRector.php` | Namespace added; class renamed from `FieldgroupToFieldsetRector`; logic identical | Minor/Style-only |
| ReplaceFileGetContentHeadersRector | [#3494126](https://drupal.org/i/3494126) | `rules/replace-file-get-content-headers-with-fileinterface-3494126.php` | `src/Drupal11/Rector/Deprecation/ReplaceFileGetContentHeadersRector.php` | Namespace added; class renamed from `FileGetContentHeadersRector`; rector uses `assert()` + `$node->args[0]->value` directly; digest guards `$node->name instanceof Name` explicitly; logic equivalent | Minor/Style-only |
| ReplaceLocaleConfigBatchFunctionsRector | [#3575254](https://drupal.org/i/3575254) | `rules/replace-deprecated-locale-batch-functions-with-their-3575254.php` | `src/Drupal11/Rector/Deprecation/ReplaceLocaleConfigBatchFunctionsRector.php` | **Completely different implementation**: digest is a config-only snippet using built-in `RenameFunctionRector`; rector implements a custom `FuncCall`-visiting rule with a `RENAME_MAP` constant | Substantial |
| ReplaceNodeAccessViewAllNodesRector | [#3038908](https://drupal.org/i/3038908) | `rules/replace-deprecated-node-access-view-all-nodes-with-oo-3038908.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeAccessViewAllNodesRector.php` | Namespace added; class renamed from `NodeAccessViewAllNodesRector`; rector uses `$this->nodeFactory` helpers; logic identical | Minor/Style-only |
| ReplaceNodeAddBodyFieldRector | [#3489266](https://drupal.org/i/3489266) | `rules/replace-deprecated-node-add-body-field-with-createbodyfield-3489266.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeAddBodyFieldRector.php` | Namespace added; class renamed from `NodeAddBodyFieldRector`; logic identical | Minor/Style-only |
| ReplaceNodeModuleProceduralFunctionsRector | [#3571623](https://drupal.org/i/3571623) | `rules/replace-deprecated-node-module-procedural-functions-with-oo-3571623.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeModuleProceduralFunctionsRector.php` | Namespace added; class renamed from `ReplaceDeprecatedNodeFunctionsRector`; both have identical logic; minor difference: digest has named private constants (`ENTITY_BUNDLE_INFO_SERVICE`, `NODE_BULK_UPDATE_CLASS`), rector inlines strings | Minor/Style-only |
| ReplaceNodeSetPreviewModeRector | [#3538277](https://drupal.org/i/3538277) | `rules/replace-deprecated-constants-with-nodepreviewmode-enum-in-3538277.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeSetPreviewModeRector.php` | Namespace added; class renamed from `NodeSetPreviewModeRector`; rector adds `NodeTypeInterface` ObjectType check (digest has none); logic otherwise identical | Substantial |
| ReplacePdoFetchConstantsRector | [#3525077](https://drupal.org/i/3525077) | `rules/replace-removed-mysql-pgsql-sqlite-driver-query-subclass-3525077.php` | `src/Drupal11/Rector/Deprecation/ReplacePdoFetchConstantsRector.php` | **Completely different rule**: digest is a config-only snippet for `RenameClassRector` removing deprecated driver subclasses; rector implements a full custom rule replacing `PDO::FETCH_*` constants with `FetchAs` enum cases in Database API calls. Same issue ID, entirely different transformation. | Substantial |
| ReplaceRecipeRunnerInstallModuleRector | [#3498026](https://drupal.org/i/3498026) | `rules/replace-deprecated-reciperunner-installmodule-with-3498026.php` | `src/Drupal11/Rector/Deprecation/ReplaceRecipeRunnerInstallModuleRector.php` | Namespace added; class renamed from `RecipeRunnerInstallModuleRector`; logic identical | Minor/Style-only |
| ReplaceSessionManagerDeleteRector | [#3577376](https://drupal.org/i/3577376) | `rules/replace-deprecated-sessionmanager-delete-with-3577376.php` | `src/Drupal11/Rector/Deprecation/ReplaceSessionManagerDeleteRector.php` | Rector integrates into AbstractDrupalCoreRector with versioned configuration; digest is a standalone AbstractRector; type-check strategy changed from `isSuperTypeOf().yes()` to `isObjectType()` | Substantial |
| ReplaceSessionWritesWithRequestSessionRector | [#3518527](https://drupal.org/i/3518527) | `rules/replace-deprecated-session-writes-with-drupal-request-3518527.php` | `src/Drupal11/Rector/Deprecation/ReplaceSessionWritesWithRequestSessionRector.php` | Namespace added; class renamed from `SessionSuperGlobalToRequestSessionRector`; logic identical | Minor/Style-only |
| ReplaceSystemPerformanceGzipKeyRector | [#3184242](https://drupal.org/i/3184242) | `rules/replace-deprecated-system-performance-css-gzip-js-gzip-3184242.php` | `src/Drupal11/Rector/Deprecation/ReplaceSystemPerformanceGzipKeyRector.php` | Namespace added; class renamed from `SystemPerformanceGzipToCompressRector`; logic identical | Minor/Style-only |
| ReplaceThemeGetSettingRector | [#3573896](https://drupal.org/i/3573896) | `rules/replace-deprecated-theme-get-setting-and-system-default-3573896.php` | `src/Drupal11/Rector/Deprecation/ReplaceThemeGetSettingRector.php` | Namespace added; class name preserved; rector inlines the theme_get_setting logic rather than extracting a private method; logic identical | Minor/Style-only |
| ReplaceUserSessionNamePropertyRector | [#3513856](https://drupal.org/i/3513856) | `rules/replace-deprecated-usersession-name-property-read-with-3513856.php` | `src/Drupal11/Rector/Deprecation/ReplaceUserSessionNamePropertyRector.php` | Namespace added; class renamed from `UserSessionNamePropertyToGetAccountNameRector`; rector adds `UserSession` ObjectType check; logic identical | Minor/Style-only |
| ReplaceViewsProceduralFunctionsRector | [#3572243](https://drupal.org/i/3572243) | `rules/replace-deprecated-views-procedural-functions-with-oo-3572243.php` | `src/Drupal11/Rector/Deprecation/ReplaceViewsProceduralFunctionsRector.php` | Namespace added; class renamed from `ReplaceDeprecatedViewsFunctionsRector`; logic identical; minor: rector uses `Node\Expr\*` fully-qualified in private methods while digest uses imported classes | Minor/Style-only |
| StatementPrefetchIteratorFetchColumnRector | [#3490200](https://drupal.org/i/3490200) | `rules/replace-deprecated-statementprefetchiterator-fetchcolumn-3490200.php` | `src/Drupal11/Rector/Deprecation/StatementPrefetchIteratorFetchColumnRector.php` | **Completely different implementation**: digest is a config-only snippet using built-in `RenameMethodRector`; rector implements a custom `MethodCall`-visiting rule with `StatementPrefetchIterator` ObjectType check | Substantial |
| StripMigrationDependenciesExpandArgRector | [#3574717](https://drupal.org/i/3574717) | `rules/strip-removed-expand-argument-from-getmigrationdependencies-3574717.php` | `src/Drupal11/Rector/Deprecation/StripMigrationDependenciesExpandArgRector.php` | Namespace added; class renamed from `RemoveMigrationDependenciesExpandArgRector`; type-check strategy: rector uses `isObjectType(new ObjectType(...))` directly; digest uses `$callerType->isSuperTypeOf(...).yes()`. Functionally identical. | Minor/Style-only |
| UseEntityTypeHasIntegerIdRector | [#3566801](https://drupal.org/i/3566801) | `rules/replace-deprecated-entity-type-integer-id-helpers-with-3566801.php` | `src/Drupal11/Rector/Deprecation/UseEntityTypeHasIntegerIdRector.php` | Namespace added; class name preserved; rector uses class constants `METHOD_OWNER_CLASS` (map) and `GET_ENTITY_TYPE_ID_KEY_TYPE_CLASS` with ObjectType checks; digest uses `SIMPLE_METHOD_NAMES` and no type-checking. Rector is significantly more type-safe. | Substantial |
| ViewsPluginHandlerManagerRector | [#3566424](https://drupal.org/i/3566424) | `rules/replace-deprecated-views-pluginmanager-and-views-3566424.php` | `src/Drupal11/Rector/Deprecation/ViewsPluginHandlerManagerRector.php` | Namespace added; class name preserved; rector uses `isName()` for class check; digest uses `isObjectType()`. Logic otherwise identical. | Minor/Style-only |

---

## Notable Changes

### ReplaceRequestTimeConstantRector
- **Source (digest):** `rules/add-timeinterface-time-argument-to-plugin-constructor-3395986.php`
- **Destination (rector):** `src/Drupal10/Rector/Deprecation/ReplaceRequestTimeConstantRector.php`
- **Drupal issue:** https://drupal.org/i/3395986
- **Summary:** The issue ID is shared but the two rules address entirely different parts of the deprecation. The digest adds `?TimeInterface $time` to `__construct()` overrides in six plugin subclasses. The rector instead replaces the `REQUEST_TIME` constant with `\Drupal::time()->getRequestTime()`.
- **Changes:**
  - Digest: Class-level AST transformation of constructor signatures across 6 named Drupal plugin parent classes
  - Rector: Simple `ConstFetch` → `StaticCall->MethodCall` replacement, a completely different rule written from scratch
  - No code from the digest was reused

### RemoveModuleHandlerDeprecatedMethodsRector
- **Source (digest):** `rules/remove-deprecated-modulehandlerinterface-writecache-and-3442009.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/RemoveModuleHandlerDeprecatedMethodsRector.php`
- **Drupal issue:** https://drupal.org/i/3442009
- **Summary:** Both remove `writeCache()` and replace `getHookInfo()` with `[]`, but the rector is more thorough.
- **Changes:**
  - Rector also removes standalone `getHookInfo()` expression statements (not just replaces the value with `[]` when used in assignment)
  - Digest leaves bare `[];` statements after `writeCache` removal; rector removes the whole statement
  - Rector factored into a private helper `isModuleHandlerMethodCall()`

### RemoveConfigSaveTrustedDataArgRector + RemoveTrustDataCallRector
- **Source (digest):** `rules/remove-deprecated-trusted-data-concept-from-drupal-config-3347842.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/RemoveConfigSaveTrustedDataArgRector.php` + `RemoveTrustDataCallRector.php`
- **Drupal issue:** https://drupal.org/i/3347842
- **Summary:** One digest class (`RemoveTrustedDataConceptRector`) covering both patterns was split into two focused rectors.
- **Changes:**
  - `RemoveConfigSaveTrustedDataArgRector` handles only `Config::save(TRUE/FALSE)` — adds `Drupal\Core\Config\Config` ObjectType check absent from digest
  - `RemoveTrustDataCallRector` handles only `trustData()` chain removal — adds `ConfigEntityInterface` ObjectType check absent from digest
  - Both rectors are more type-safe than the combined digest

### RemoveAutomatedCronSubmitHandlerRector
- **Source (digest):** `rules/remove-deprecated-automated-cron-settings-submit-calls-and-3566768.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/RemoveAutomatedCronSubmitHandlerRector.php`
- **Drupal issue:** https://drupal.org/i/3566768
- **Summary:** Digest registered two rules: a custom class for `$form['#submit'][]` and `RemoveFuncCallRector` for direct function calls. Rector only implements the array-append removal.
- **Changes:**
  - Class renamed from `RemoveAutomatedCronSettingsSubmitHandlerRector`
  - Direct `automated_cron_settings_submit($form, $form_state)` function call removal (via `RemoveFuncCallRector`) is not ported into the rector

### RemoveCacheExpireOverrideRector
- **Source (digest):** `rules/remove-deprecated-cacheexpire-overrides-from-views-3576556.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/RemoveCacheExpireOverrideRector.php`
- **Drupal issue:** https://drupal.org/i/3576556
- **Summary:** Rector significantly improves the class-hierarchy detection logic compared to the digest.
- **Changes:**
  - Rector adds `PARENT_FQCNS` constant listing fully-qualified class names (all four known parent classes)
  - Rector adds `'None'` to `PARENT_SHORT_NAMES`
  - Rector uses `str_ends_with($parentName, '\\' . $short)` for namespace-relative names
  - Rector's PHPStan fallback constructs `isSuperTypeOf($extendsType)` correctly with both operands as ObjectType; digest uses `$objectType->isSuperTypeOf($extendsType)` with only one side properly typed

### RemoveHandlerBaseDefineExtraOptionsRector
- **Source (digest):** `rules/remove-overrides-of-deprecated-handlerbase-3485084.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/RemoveHandlerBaseDefineExtraOptionsRector.php`
- **Drupal issue:** https://drupal.org/i/3485084
- **Summary:** Rector broadens detection to five additional short class names not in the digest, and uses a different exclusion approach for the base class itself.
- **Changes:**
  - Rector adds `PARENT_SHORT_NAMES = ['HandlerBase', 'FieldHandlerBase', 'FilterPluginBase', 'SortPluginBase', 'ArgumentPluginBase', 'RelationshipPluginBase']`
  - Digest used `Identifier` check to avoid modifying `HandlerBase` itself; rector uses FQCN/short-name matching instead
  - Rector adds `isObjectType` PHPStan fallback

### LoadAllIncludesRector
- **Source (digest):** `rules/replace-deprecated-modulehandler-loadallincludes-with-3536431.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/LoadAllIncludesRector.php`
- **Drupal issue:** https://drupal.org/i/3536431
- **Summary:** Rector adds a `ModuleHandlerInterface` type guard that was missing from the digest.
- **Changes:**
  - Rector calls `$this->isObjectType($methodCall->var, new ObjectType('Drupal\Core\Extension\ModuleHandlerInterface'))` before rewriting
  - Digest skips this type check, rewriting any `loadAllIncludes()` call

### MigrateSqlGetMigrationPluginManagerRector
- **Source (digest):** `rules/replace-deprecated-sql-getmigrationpluginmanager-with-3439369.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/MigrateSqlGetMigrationPluginManagerRector.php`
- **Drupal issue:** https://drupal.org/i/3439369
- **Summary:** The type-check approach is inverted: rector whitelists `Sql` class; digest blacklists `Migration` class.
- **Changes:**
  - Rector checks `isObjectType($node->var, new ObjectType('Drupal\migrate\Plugin\migrate\id_map\Sql'))` — only rewrites if caller is `Sql`
  - Digest excludes `Migration` via `isObjectType(Migration::class)` check but allows any other caller
  - Rector approach is more restrictive and precise

### NodeStorageDeprecatedMethodsRector
- **Source (digest):** `rules/replace-deprecated-nodestorage-revisionids-and-3396062.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/NodeStorageDeprecatedMethodsRector.php`
- **Drupal issue:** https://drupal.org/i/3396062
- **Summary:** Rector adds `countDefaultLanguageRevisions` removal not present in the digest.
- **Changes:**
  - Rector registers `Expression::class` in `getNodeTypes()` to catch statement-level calls
  - Rector removes `countDefaultLanguageRevisions()` expression statements entirely via `NodeVisitor::REMOVE_NODE`
  - Digest only handled `revisionIds` and `userRevisionIds`

### PluginBaseIsConfigurableRector
- **Source (digest):** `rules/replace-deprecated-pluginbase-isconfigurable-with-3459533.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/PluginBaseIsConfigurableRector.php`
- **Drupal issue:** https://drupal.org/i/3459533
- **Summary:** Rector adds a `PluginBase` ObjectType guard; digest relies only on the `$this->isConfigurable()` pattern.
- **Changes:**
  - Rector calls `$this->isObjectType($node->var, new ObjectType('Drupal\Component\Plugin\PluginBase'))` in addition to variable/name checks
  - Digest never verifies the object type, so it could rewrite `$this->isConfigurable()` in any class with that method

### RemoveModuleHandlerAddModuleCallsRector
- **Source (digest):** `rules/remove-deprecated-modulehandlerinterface-addmodule-and-3528899.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/RemoveModuleHandlerAddModuleCallsRector.php`
- **Drupal issue:** https://drupal.org/i/3528899
- **Summary:** Rector also checks against the concrete `ModuleHandler` class in addition to the interface.
- **Changes:**
  - Rector iterates over both `ModuleHandlerInterface` and `ModuleHandler` in the ObjectType check
  - Digest only checks `ModuleHandlerInterface`

### RemoveRootFromConvertDbUrlRector
- **Source (digest):** `rules/remove-deprecated-string-root-from-database-3522513.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/RemoveRootFromConvertDbUrlRector.php`
- **Drupal issue:** https://drupal.org/i/3522513
- **Summary:** Rector recognizes more expression types as valid `$root` arguments to remove.
- **Changes:**
  - Rector adds `StaticPropertyFetch` and `MethodCall` to the recognized second-arg forms (digest omits these two)
  - Rector imports those types explicitly at the top of the file

### RemoveSetUriCallbackRector
- **Source (digest):** `rules/remove-deprecated-entitytypeinterface-seturicallback-calls-2667040.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/RemoveSetUriCallbackRector.php`
- **Drupal issue:** https://drupal.org/i/2667040
- **Summary:** Rector adds `EntityTypeInterface` type guard throughout; digest has none.
- **Changes:**
  - Rector calls `isObjectType($node->expr->var, new ObjectType('Drupal\Core\Entity\EntityTypeInterface'))` in both the standalone and fluent-chain cases
  - Digest matches any `setUriCallback()` call on any object

### RemoveTwigNodeTransTagArgumentRector
- **Source (digest):** `rules/remove-deprecated-tag-argument-from-twignodetrans-3473440.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/RemoveTwigNodeTransTagArgumentRector.php`
- **Drupal issue:** https://drupal.org/i/3473440
- **Summary:** Different strategies for arg removal and class matching.
- **Changes:**
  - Rector checks `count($node->args) === 6` exactly and uses `array_pop()`; digest uses `isset($node->args[5])` and `array_splice($node->args, 5)` (removes from index 5 onward — handles extra args too)
  - Rector also matches the short class name `TwigNodeTrans` without namespace

### ReplaceEditorLoadRector
- **Source (digest):** `rules/replace-deprecated-editor-load-with-entity-storage-load-3447794.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/ReplaceEditorLoadRector.php`
- **Drupal issue:** https://drupal.org/i/3447794
- **Summary:** Rector uses framework helpers (`$this->nodeFactory`) for cleaner code; adds arg count guard.
- **Changes:**
  - Rector adds `count($node->args) !== 1` guard (digest does not)
  - Rector uses `$this->nodeFactory->createStaticCall()` and `createMethodCall()` instead of manual AST construction

### ReplaceEntityOriginalPropertyRector
- **Source (digest):** `rules/replace-deprecated-entity-original-magic-property-with-3571065.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/ReplaceEntityOriginalPropertyRector.php`
- **Drupal issue:** https://drupal.org/i/3571065
- **Summary:** Rector adds nullsafe property fetch support and EntityInterface type check — both missing from digest.
- **Changes:**
  - Rector registers `NullsafePropertyFetch::class` in `getNodeTypes()` and handles `$entity?->original` → `$entity?->getOriginal()`
  - Rector adds `EntityInterface` ObjectType check on `PropertyFetch` (digest has no type check)
  - Digest would rewrite any `->original` property on any object; rector only rewrites on `EntityInterface` instances

### ReplaceNodeSetPreviewModeRector
- **Source (digest):** `rules/replace-deprecated-constants-with-nodepreviewmode-enum-in-3538277.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/ReplaceNodeSetPreviewModeRector.php`
- **Drupal issue:** https://drupal.org/i/3538277
- **Summary:** Rector adds `NodeTypeInterface` type guard; digest has none.
- **Changes:**
  - Rector calls `isObjectType($node->var, new ObjectType('Drupal\node\NodeTypeInterface'))`
  - Digest rewrites `setPreviewMode(DRUPAL_DISABLED/0/1/2)` on any object

### ReplacePdoFetchConstantsRector
- **Source (digest):** `rules/replace-removed-mysql-pgsql-sqlite-driver-query-subclass-3525077.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/ReplacePdoFetchConstantsRector.php`
- **Drupal issue:** https://drupal.org/i/3525077
- **Summary:** The issue ID is shared but the two rules handle entirely different aspects of this Drupal issue.
- **Changes:**
  - Digest: config snippet using `RenameClassRector` to repoint nine empty driver-specific query subclasses to `Drupal\Core\Database\Query\*`
  - Rector: custom rule converting `PDO::FETCH_*` constants to `FetchAs` enum cases in `setFetchMode`/`fetch`/`fetchAll`/`fetchAllAssoc` calls plus `'fetch'` array keys
  - Completely different rule written from scratch

### RenameStopProceduralHookScanRector
- **Source (digest):** `rules/rename-stopproceduralhookscan-attribute-to-3495943.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/RenameStopProceduralHookScanRector.php`
- **Drupal issue:** https://drupal.org/i/3495943
- **Summary:** Digest is a trivial config snippet; rector is a full custom implementation.
- **Changes:**
  - Digest uses `RenameClassRector` configuration (2 lines of real logic)
  - Rector implements `UseUse` and `Attribute` node visiting to rename both the `use` statement and the attribute usage site, preserving correct formatting
  - Rector approach avoids the risk of `RenameClassRector` rewriting class body references unexpectedly

### ReplaceLocaleConfigBatchFunctionsRector
- **Source (digest):** `rules/replace-deprecated-locale-batch-functions-with-their-3575254.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/ReplaceLocaleConfigBatchFunctionsRector.php`
- **Drupal issue:** https://drupal.org/i/3575254
- **Summary:** Digest is a config snippet; rector is a full custom rule.
- **Changes:**
  - Digest uses `RenameFunctionRector` configuration
  - Rector implements `FuncCall` node visiting with a `RENAME_MAP` constant, providing more control and testability

### StatementPrefetchIteratorFetchColumnRector
- **Source (digest):** `rules/replace-deprecated-statementprefetchiterator-fetchcolumn-3490200.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/StatementPrefetchIteratorFetchColumnRector.php`
- **Drupal issue:** https://drupal.org/i/3490200
- **Summary:** Digest is a config snippet; rector adds full type-safe implementation.
- **Changes:**
  - Digest uses `RenameMethodRector` configuration (renames `fetchColumn` → `fetchField` on `StatementPrefetchIterator`)
  - Rector implements `MethodCall` node visiting with a `StatementPrefetchIterator` ObjectType check
  - Rector approach is equivalent but written as testable custom rule

### ReplaceCommentManagerGetCountNewCommentsRector
- **Source (digest):** `rules/replace-deprecated-commentmanagerinterface-3543035.php` (issue 3543035; rector maps to issue 3551729 — closest available match)
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/ReplaceCommentManagerGetCountNewCommentsRector.php`
- **Drupal issue:** https://drupal.org/i/3551729
- **Summary:** Rector integrates into the versioned configuration framework; digest is a plain AbstractRector.
- **Changes:**
  - Rector extends `AbstractDrupalCoreRector` with `DrupalIntroducedVersionConfiguration('11.3.0')`
  - Rector uses `refactorWithConfiguration()` instead of `refactor()`
  - Digest uses `AbstractRector` directly; logic otherwise identical

### ReplaceSessionManagerDeleteRector
- **Source (digest):** `rules/replace-deprecated-sessionmanager-delete-with-3577376.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/ReplaceSessionManagerDeleteRector.php`
- **Drupal issue:** https://drupal.org/i/3577376
- **Summary:** Rector integrates into versioned configuration framework; digest is standalone.
- **Changes:**
  - Rector extends `AbstractDrupalCoreRector` with `DrupalIntroducedVersionConfiguration`
  - Rector uses `isObjectType()` for type check; digest uses `$sessionManagerType->isSuperTypeOf($callerType)->yes()`
  - Logic otherwise identical

### UseEntityTypeHasIntegerIdRector
- **Source (digest):** `rules/replace-deprecated-entity-type-integer-id-helpers-with-3566801.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/UseEntityTypeHasIntegerIdRector.php`
- **Drupal issue:** https://drupal.org/i/3566801
- **Summary:** Rector adds per-class ObjectType type guards absent from the digest.
- **Changes:**
  - Rector defines `METHOD_OWNER_CLASS` map: `entityTypeSupportsComments` → `CommentTypeForm`, `hasIntegerId` → `OverridesSectionStorage`
  - Rector adds `GET_ENTITY_TYPE_ID_KEY_TYPE_CLASS = 'DefaultHtmlRouteProvider'` constant with ObjectType check
  - Digest uses `SIMPLE_METHOD_NAMES` with no type checks — would rewrite any `$this->entityTypeSupportsComments()` or `$this->hasIntegerId()` call on any class
  - Rector is significantly safer against false positives

### RemoveViewsRowCacheKeysRector
- **Source (digest):** `rules/remove-deprecated-cachepluginbase-getrowcachekeys-and-3564937.php`
- **Destination (rector):** `src/Drupal11/Rector/Deprecation/RemoveViewsRowCacheKeysRector.php`
- **Drupal issue:** https://drupal.org/i/3564958
- **Summary:** Rector adds CachePluginBase ObjectType guard absent from digest.
- **Changes:**
  - Rector calls `isObjectType($item->value->var, new ObjectType('Drupal\views\Plugin\views\cache\CachePluginBase'))` before removing the array item
  - Digest matches any `getRowCacheKeys`/`getRowId` method call on any object
  - Rector avoids false positives on unrelated classes with the same method names

### ReplaceModuleHandlerGetNameRector
- **Source (digest):** `rules/replace-removed-modulehandlerinterface-getname-with-3571063.php`
- **Destination (rector):** `src/Drupal10/Rector/Deprecation/ReplaceModuleHandlerGetNameRector.php`
- **Drupal issue:** https://drupal.org/i/3571063
- **Summary:** Rector integrates into AbstractDrupalCoreRector framework with versioned configuration.
- **Changes:**
  - Rector extends `AbstractDrupalCoreRector` and uses `DrupalIntroducedVersionConfiguration`
  - Rector uses `refactorWithConfiguration()` instead of `refactor()`
  - Digest uses plain `AbstractRector` — same transformation logic otherwise

### ReplaceRebuildThemeDataRector
- **Source (digest):** `rules/replace-removed-themehandlerinterface-rebuildthemedata-with-3571068.php`
- **Destination (rector):** `src/Drupal10/Rector/Deprecation/ReplaceRebuildThemeDataRector.php`
- **Drupal issue:** https://drupal.org/i/3571068
- **Summary:** Rector integrates into AbstractDrupalCoreRector framework with versioned configuration.
- **Changes:**
  - Rector extends `AbstractDrupalCoreRector` and uses `DrupalIntroducedVersionConfiguration`
  - Rector adds `ThemeHandlerInterface` ObjectType check (digest lacks this)
  - Logic otherwise identical

---

## Style-only / Minimal Changes

These rectors show only namespace addition, class renaming, minor import reorganization, or trivial wording differences in doc blocks/rule descriptions. The core transformation logic is identical to the digest.

- `ErrorCurrentErrorHandlerRector` — source: `rules/replace-error-currenterrorhandler-with-get-error-handler-3526515.php`, dest: `src/Drupal11/Rector/Deprecation/ErrorCurrentErrorHandlerRector.php`
- `FileSystemBasenameToNativeRector` — source: `rules/replace-filesysteminterface-basename-with-native-basename-3530461.php`, dest: `src/Drupal11/Rector/Deprecation/FileSystemBasenameToNativeRector.php`
- `RemoveLinkWidgetValidateTitleElementRector` — source: `rules/remove-deprecated-linkwidget-validatetitleelement-calls-3093118.php`, dest: `src/Drupal11/Rector/Deprecation/RemoveLinkWidgetValidateTitleElementRector.php`
- `RemoveStateCacheSettingRector` — source: `rules/remove-deprecated-settings-state-cache-assignment-3436954.php`, dest: `src/Drupal11/Rector/Deprecation/RemoveStateCacheSettingRector.php`
- `RemoveUpdaterPostInstallMethodsRector` — source: `rules/remove-deprecated-updater-postinstall-postinstalltasks-3417136.php`, dest: `src/Drupal11/Rector/Deprecation/RemoveUpdaterPostInstallMethodsRector.php`
- `ReplaceAlphadecimalToIntNullRector` — source: `rules/replace-deprecated-number-alphadecimaltoint-null-calls-with-3442810.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceAlphadecimalToIntNullRector.php`
- `ReplaceCommentUriRector` — source: `rules/replace-deprecated-comment-uri-with-comment-permalink-2010202.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceCommentUriRector.php`
- `ReplaceDateTimeRangeConstantsRector` — source: `rules/replace-removed-datetimerangeconstantsinterface-constants-3574901.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceDateTimeRangeConstantsRector.php`
- `ReplaceEntityReferenceRecursiveLimitRector` — source: `rules/replace-deprecated-entityreferenceentityformatter-recursive-2940605.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceEntityReferenceRecursiveLimitRector.php`
- `ReplaceFieldgroupToFieldsetRector` — source: `rules/replace-deprecated-type-fieldgroup-with-type-fieldset-3512254.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceFieldgroupToFieldsetRector.php`
- `ReplaceFileGetContentHeadersRector` — source: `rules/replace-file-get-content-headers-with-fileinterface-3494126.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceFileGetContentHeadersRector.php`
- `ReplaceNodeAccessViewAllNodesRector` — source: `rules/replace-deprecated-node-access-view-all-nodes-with-oo-3038908.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceNodeAccessViewAllNodesRector.php`
- `ReplaceNodeAddBodyFieldRector` — source: `rules/replace-deprecated-node-add-body-field-with-createbodyfield-3489266.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceNodeAddBodyFieldRector.php`
- `ReplaceNodeModuleProceduralFunctionsRector` — source: `rules/replace-deprecated-node-module-procedural-functions-with-oo-3571623.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceNodeModuleProceduralFunctionsRector.php`
- `ReplaceRecipeRunnerInstallModuleRector` — source: `rules/replace-deprecated-reciperunner-installmodule-with-3498026.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceRecipeRunnerInstallModuleRector.php`
- `ReplaceSessionWritesWithRequestSessionRector` — source: `rules/replace-deprecated-session-writes-with-drupal-request-3518527.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceSessionWritesWithRequestSessionRector.php`
- `ReplaceSystemPerformanceGzipKeyRector` — source: `rules/replace-deprecated-system-performance-css-gzip-js-gzip-3184242.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceSystemPerformanceGzipKeyRector.php`
- `ReplaceThemeGetSettingRector` — source: `rules/replace-deprecated-theme-get-setting-and-system-default-3573896.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceThemeGetSettingRector.php`
- `ReplaceUserSessionNamePropertyRector` — source: `rules/replace-deprecated-usersession-name-property-read-with-3513856.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceUserSessionNamePropertyRector.php`
- `ReplaceViewsProceduralFunctionsRector` — source: `rules/replace-deprecated-views-procedural-functions-with-oo-3572243.php`, dest: `src/Drupal11/Rector/Deprecation/ReplaceViewsProceduralFunctionsRector.php`
- `StripMigrationDependenciesExpandArgRector` — source: `rules/strip-removed-expand-argument-from-getmigrationdependencies-3574717.php`, dest: `src/Drupal11/Rector/Deprecation/StripMigrationDependenciesExpandArgRector.php`
- `ViewsPluginHandlerManagerRector` — source: `rules/replace-deprecated-views-pluginmanager-and-views-3566424.php`, dest: `src/Drupal11/Rector/Deprecation/ViewsPluginHandlerManagerRector.php`
