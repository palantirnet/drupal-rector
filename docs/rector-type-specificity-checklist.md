# Rector Type-Specificity Checklist

Rectors that match a method call, property access, or `$this` reference **by name only** — without verifying the owning class or interface — will transform any unrelated class that happens to share that name. This checklist tracks every rector added in the `feature/digest-rectors` branch against that criterion.

Use `/rector-type-check-review <RectorClassName>` to fix an AT-RISK rector interactively.

---

## Legend

| Verdict | Meaning |
|---------|---------|
| ✅ SAFE | Correct `isObjectType` guard, targets global functions/constants, or name is so specific collisions are implausible |
| ⚠️ AT-RISK | Matches method/property/`$this` by name alone without owning-class verification |
| 🔵 EXEMPT | Operates on a **class declaration** (`Class_` node) and checks the parent class before acting |

---

## Drupal 10 Rectors

| Rector | Matches | Guard | Verdict | Notes |
|--------|---------|-------|---------|-------|
| `ReplaceModuleHandlerGetNameRector` | `->getName()` | `isObjectType(ModuleHandlerInterface)` | ✅ SAFE | |
| `ReplaceRebuildThemeDataRector` | `->rebuildThemeData()` | `isObjectType(ThemeHandlerInterface)` | ✅ SAFE | Fixed |
| `ReplaceRequestTimeConstantRector` | `REQUEST_TIME` constant | n/a | ✅ SAFE | Global constant — no owning class |

---

## Drupal 11 Rectors

| Rector | Matches | Guard | Verdict | Notes |
|--------|---------|-------|---------|-------|
| `ErrorCurrentErrorHandlerRector` | `Error::currentErrorHandler()` static | `isObjectType(Error)` | ✅ SAFE | |
| `FileSystemBasenameToNativeRector` | `->basename()` | `isObjectType(FileSystemInterface\|FileSystem)` | ✅ SAFE | |
| `LoadAllIncludesRector` | `->loadAllIncludes()` | `isObjectType(ModuleHandlerInterface)` | ✅ SAFE | |
| `MigrateSqlGetMigrationPluginManagerRector` | `$this->getMigrationPluginManager()` | `isObjectType(Sql)` | ✅ SAFE | |
| `NodeStorageDeprecatedMethodsRector` | `->revisionIds()` etc. | `isObjectType(NodeStorageInterface)` | ✅ SAFE | |
| `PluginBaseIsConfigurableRector` | `$this->isConfigurable()` | `isObjectType(PluginBase)` | ✅ SAFE | Fixed |
| `RemoveAutomatedCronSubmitHandlerRector` | `$form['#submit'][]` string literal | n/a | ✅ SAFE | Matches specific string value, not a class member |
| `RemoveCacheExpireOverrideRector` | `cacheExpire()` method declaration | `extends` + `isObjectType` fallback on `Class_` | 🔵 EXEMPT | |
| `RemoveConfigSaveTrustedDataArgRector` | `->save(TRUE\|FALSE)` | `isObjectType(Config)` | ✅ SAFE | Fixed |
| `RemoveHandlerBaseDefineExtraOptionsRector` | `defineExtraOptions()` declaration | `extends` check on `Class_` | 🔵 EXEMPT | |
| `RemoveLinkWidgetValidateTitleElementRector` | `LinkWidget::validateTitleElement()` static | `isName(LinkWidget)` | ✅ SAFE | |
| `RemoveModuleHandlerAddModuleCallsRector` | `->addModule()`, `->addProfile()` | `isObjectType(ModuleHandlerInterface\|ModuleHandler)` | ✅ SAFE | |
| `RemoveModuleHandlerDeprecatedMethodsRector` | `->writeCache()`, `->getHookInfo()` | `isObjectType(ModuleHandlerInterface)` | ✅ SAFE | |
| `RemoveRootFromConvertDbUrlRector` | `Database::convertDbUrlToConnectionInfo()` static | `isName(Database)` | ✅ SAFE | |
| `RemoveSetUriCallbackRector` | `->setUriCallback()` | `isObjectType(EntityTypeInterface)` | ✅ SAFE | Fixed |
| `RemoveStateCacheSettingRector` | `$settings['state_cache']` array key | n/a | ✅ SAFE | Specific variable + key pattern |
| `RemoveTrustDataCallRector` | `->trustData()` | `isObjectType(ConfigEntityInterface)` | ✅ SAFE | Fixed |
| `RemoveTwigNodeTransTagArgumentRector` | `new TwigNodeTrans(...)` | `isName(TwigNodeTrans)` | ✅ SAFE | |
| `RemoveUpdaterPostInstallMethodsRector` | `postInstall()`, `postInstallTasks()` declarations | `extends` check on `Class_` | 🔵 EXEMPT | |
| `RemoveViewsRowCacheKeysRector` | array values: `->getRowCacheKeys()`, `->getRowId()` | `isObjectType(CachePluginBase)` | ✅ SAFE | Fixed |
| `RenameStopProceduralHookScanRector` | `StopProceduralHookScan` attribute/use | `isName` on FQCN | ✅ SAFE | |
| `ReplaceAlphadecimalToIntNullRector` | `Number::alphadecimalToInt()` static | `isObjectType(Number)` | ✅ SAFE | |
| `ReplaceCommentManagerGetCountNewCommentsRector` | `->getCountNewComments()` | `isObjectType(CommentManagerInterface)` | ✅ SAFE | |
| `ReplaceCommentUriRector` | `comment_uri()` function | n/a | ✅ SAFE | Global function |
| `ReplaceDateTimeRangeConstantsRector` | class constant fetch + function | `isName` on interface | ✅ SAFE | |
| `ReplaceEditorLoadRector` | `editor_load()` function | n/a | ✅ SAFE | Global function |
| `ReplaceEntityOriginalPropertyRector` | `->original` property | `isObjectType(EntityInterface)` | ✅ SAFE | Fixed |
| `ReplaceEntityReferenceRecursiveLimitRector` | `RECURSIVE_RENDER_LIMIT` class const | `isName` on target classes | ✅ SAFE | |
| `ReplaceFieldgroupToFieldsetRector` | `'#type' => 'fieldgroup'` array literal | n/a | ✅ SAFE | String literal match |
| `ReplaceFileGetContentHeadersRector` | `file_get_content_headers()` function | n/a | ✅ SAFE | Global function |
| `ReplaceLocaleConfigBatchFunctionsRector` | `locale_config_batch_*()` functions | n/a | ✅ SAFE | Global functions |
| `ReplaceNodeAccessViewAllNodesRector` | `node_access_view_all_nodes()` etc. | n/a | ✅ SAFE | Global functions |
| `ReplaceNodeAddBodyFieldRector` | `node_add_body_field()` function | n/a | ✅ SAFE | Global function |
| `ReplaceNodeModuleProceduralFunctionsRector` | `node_type_get_names()` etc. | n/a | ✅ SAFE | Global functions |
| `ReplaceNodeSetPreviewModeRector` | `->setPreviewMode(0\|1\|2\|CONST)` | `isObjectType(NodeTypeInterface)` | ✅ SAFE | Fixed |
| `ReplacePdoFetchConstantsRector` | `PDO::FETCH_*` constants | `isName(PDO)` on const fetch | ✅ SAFE | |
| `ReplaceRecipeRunnerInstallModuleRector` | `RecipeRunner::installModule()` static | `isName(RecipeRunner)` | ✅ SAFE | |
| `ReplaceSessionManagerDeleteRector` | `->delete()` | `isObjectType(SessionManager)` | ✅ SAFE | |
| `ReplaceSessionWritesWithRequestSessionRector` | `$_SESSION[...]` superglobal | n/a | ✅ SAFE | Specific superglobal |
| `ReplaceSystemPerformanceGzipKeyRector` | `->get()`/`->set()` on config chain | chain inspection for `'system.performance'` key | ✅ SAFE | Custom chain guard |
| `ReplaceThemeGetSettingRector` | `theme_get_setting()` etc. | n/a | ✅ SAFE | Global functions |
| `ReplaceUserSessionNamePropertyRector` | `->name` property | `isObjectType(UserSession)` + skips `$this` | ✅ SAFE | |
| `ReplaceViewsProceduralFunctionsRector` | `views_*()` functions | n/a | ✅ SAFE | Global functions |
| `StatementPrefetchIteratorFetchColumnRector` | `->fetchColumn()` | `isObjectType(StatementPrefetchIterator)` | ✅ SAFE | Fixed |
| `StripMigrationDependenciesExpandArgRector` | `->getMigrationDependencies()` | `isObjectType(MigrationInterface)` | ✅ SAFE | |
| `UseEntityTypeHasIntegerIdRector` | `$this->getEntityTypeIdKeyType()` etc. | `isObjectType` per-method via `METHOD_OWNER_CLASS` map | ✅ SAFE | Fixed |
| `ViewsPluginHandlerManagerRector` | `Views::pluginManager()` static | `isName(Views)` | ✅ SAFE | |
| `FunctionCallRemovalRector` (generic) | configured function names | configuration-driven name match | ✅ SAFE | Targets global functions only |

---

## AT-RISK Summary

All 10 AT-RISK rectors have been fixed. ✅

| # | Rector | Guard added | Drupal class/interface used |
|---|--------|------------|------------------------------|
| 1 | `ReplaceRebuildThemeDataRector` | `isObjectType` on `->rebuildThemeData()` caller | `Drupal\Core\Extension\ThemeHandlerInterface` |
| 2 | `PluginBaseIsConfigurableRector` | `isObjectType` on `$this` | `Drupal\Component\Plugin\PluginBase` |
| 3 | `RemoveConfigSaveTrustedDataArgRector` | `isObjectType` on `->save()` caller | `Drupal\Core\Config\Config` |
| 4 | `RemoveSetUriCallbackRector` | `isObjectType` on `->setUriCallback()` caller | `Drupal\Core\Entity\EntityTypeInterface` |
| 5 | `RemoveTrustDataCallRector` | `isObjectType` on `->trustData()` caller | `Drupal\Core\Config\Entity\ConfigEntityInterface` |
| 6 | `RemoveViewsRowCacheKeysRector` | `isObjectType` on method-call receiver inside array item | `Drupal\views\Plugin\views\cache\CachePluginBase` |
| 7 | `ReplaceEntityOriginalPropertyRector` | `isObjectType` on `->original` variable | `Drupal\Core\Entity\EntityInterface` |
| 8 | `ReplaceNodeSetPreviewModeRector` | `isObjectType` on `->setPreviewMode()` caller | `Drupal\node\NodeTypeInterface` |
| 9 | `StatementPrefetchIteratorFetchColumnRector` | `isObjectType` on `->fetchColumn()` caller | `Drupal\Core\Database\StatementPrefetchIterator` |
| 10 | `UseEntityTypeHasIntegerIdRector` | per-method `isObjectType` via `METHOD_OWNER_CLASS` map | `DefaultHtmlRouteProvider`, `CommentTypeForm`, `OverridesSectionStorage` |
