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

| Rector | Matches | Guard | Verdict | Issue | Change Record | Notes |
|--------|---------|-------|---------|-------|---------------|-------|
| `ReplaceModuleHandlerGetNameRector` | `->getName()` | `isObjectType(ModuleHandlerInterface)` | ✅ SAFE | 3571063 | — | |
| `ReplaceRebuildThemeDataRector` | `->rebuildThemeData()` | `isObjectType(ThemeHandlerInterface)` | ✅ SAFE | 3571068 | — | Fixed |
| `ReplaceRequestTimeConstantRector` | `REQUEST_TIME` constant | n/a | ✅ SAFE | 3395986 | 3395991 | Global constant — no owning class |

---

## Drupal 11 Rectors

| Rector | Matches | Guard | Verdict | Issue | Change Record | Notes |
|--------|---------|-------|---------|-------|---------------|-------|
| `ErrorCurrentErrorHandlerRector` | `Error::currentErrorHandler()` static | `isObjectType(Error)` | ✅ SAFE | 3526515 | 3529500 | |
| `FileSystemBasenameToNativeRector` | `->basename()` | `isObjectType(FileSystemInterface\|FileSystem)` | ✅ SAFE | 3530461 | 3530869 | |
| `LoadAllIncludesRector` | `->loadAllIncludes()` | `isObjectType(ModuleHandlerInterface)` | ✅ SAFE | 3536431 | 3536432 | Fixed |
| `MigrateSqlGetMigrationPluginManagerRector` | `$this->getMigrationPluginManager()` | `isObjectType(Sql)` | ✅ SAFE | 3439369 | 3442785 | |
| `NodeStorageDeprecatedMethodsRector` | `->revisionIds()` etc. | `isObjectType(NodeStorageInterface)` | ✅ SAFE | 3396062 | 3519187 | |
| `PluginBaseIsConfigurableRector` | `$this->isConfigurable()` | `isObjectType(PluginBase)` | ✅ SAFE | 3459533 | 3459535 | Fixed |
| `RemoveAutomatedCronSubmitHandlerRector` | `$form['#submit'][]` string literal | n/a | ✅ SAFE | 3566768 | 3566774 | Matches specific string value, not a class member |
| `RemoveCacheExpireOverrideRector` | `cacheExpire()` method declaration | exact FQCN list + `isObjectType` fallback on `Class_` | 🔵 EXEMPT | 3576556 | 3576855 | Fixed |
| `RemoveConfigSaveTrustedDataArgRector` | `->save(TRUE\|FALSE)` | `isObjectType(Config)` | ✅ SAFE | 3347842 | 3348180 | Fixed |
| `RemoveHandlerBaseDefineExtraOptionsRector` | `defineExtraOptions()` declaration | `extends` check on `Class_` | 🔵 EXEMPT | 3485084 | 3486781 | |
| `RemoveLinkWidgetValidateTitleElementRector` | `LinkWidget::validateTitleElement()` static | `isName(LinkWidget)` | ✅ SAFE | 3093118 | 3554139 | |
| `RemoveModuleHandlerAddModuleCallsRector` | `->addModule()`, `->addProfile()` | `isObjectType(ModuleHandlerInterface\|ModuleHandler)` | ✅ SAFE | 3528899 | 3550193 | |
| `RemoveModuleHandlerDeprecatedMethodsRector` | `->writeCache()`, `->getHookInfo()` | `isObjectType(ModuleHandlerInterface)` | ✅ SAFE | 3442009 | — | |
| `RemoveRootFromConvertDbUrlRector` | `Database::convertDbUrlToConnectionInfo()` static | `isName(Database)` | ✅ SAFE | 3522513 | 3511287 | |
| `RemoveSetUriCallbackRector` | `->setUriCallback()` | `isObjectType(EntityTypeInterface)` | ✅ SAFE | 2667040 | 3575062 | Fixed |
| `RemoveStateCacheSettingRector` | `$settings['state_cache']` array key | n/a | ✅ SAFE | 3436954 | 3443018 | Specific variable + key pattern |
| `RemoveTrustDataCallRector` | `->trustData()` | `isObjectType(ConfigEntityInterface)` | ✅ SAFE | 3347842 | 3348180 | Fixed |
| `RemoveTwigNodeTransTagArgumentRector` | `new TwigNodeTrans(...)` | `isName(TwigNodeTrans)` | ✅ SAFE | 3473440 | — | |
| `RemoveUpdaterPostInstallMethodsRector` | `postInstall()`, `postInstallTasks()` declarations | `extends` check on `Class_` | 🔵 EXEMPT | 3417136 | 3571399 | |
| `RemoveViewsRowCacheKeysRector` | array values: `->getRowCacheKeys()`, `->getRowId()` | `isObjectType(CachePluginBase)` | ✅ SAFE | 3564937 | 3564958 | Fixed |
| `RenameStopProceduralHookScanRector` | `StopProceduralHookScan` attribute/use | `isName` on FQCN | ✅ SAFE | 3495943 | 3490771 | |
| `ReplaceAlphadecimalToIntNullRector` | `Number::alphadecimalToInt()` static | `isObjectType(Number)` | ✅ SAFE | 3442810 | 3494472 | |
| `ReplaceCommentManagerGetCountNewCommentsRector` | `->getCountNewComments()` | `isObjectType(CommentManagerInterface)` | ✅ SAFE | 3543035 | 3551729 | |
| `ReplaceCommentUriRector` | `comment_uri()` function | n/a | ✅ SAFE | 2010202 | 3384294 | Global function |
| `ReplaceDateTimeRangeConstantsRector` | class constant fetch + function | `isName` on interface | ✅ SAFE | 3574901 | — | |
| `ReplaceEditorLoadRector` | `editor_load()` function | n/a | ✅ SAFE | 3447794 | 3509245 | Global function |
| `ReplaceEntityOriginalPropertyRector` | `->original` property | `isObjectType(EntityInterface)` | ✅ SAFE | 3571065 | — | Fixed |
| `ReplaceEntityReferenceRecursiveLimitRector` | `RECURSIVE_RENDER_LIMIT` class const | `isName` on target classes | ✅ SAFE | 2940605 | 3316878 | |
| `ReplaceFieldgroupToFieldsetRector` | `'#type' => 'fieldgroup'` array literal | n/a | ✅ SAFE | 3512254 | 3515272 | String literal match |
| `ReplaceFileGetContentHeadersRector` | `file_get_content_headers()` function | n/a | ✅ SAFE | 3494126 | 3494172 | Global function |
| `ReplaceLocaleConfigBatchFunctionsRector` | `locale_config_batch_*()` functions | n/a | ✅ SAFE | 3575254 | — | Global functions |
| `ReplaceNodeAccessViewAllNodesRector` | `node_access_view_all_nodes()` etc. | n/a | ✅ SAFE | 3038908 | 3038909 | Global functions |
| `ReplaceNodeAddBodyFieldRector` | `node_add_body_field()` function | n/a | ✅ SAFE | 3489266 | 3516778 | Global function |
| `ReplaceNodeModuleProceduralFunctionsRector` | `node_type_get_names()` etc. | n/a | ✅ SAFE | 3571623 | — | Global functions |
| `ReplaceNodeSetPreviewModeRector` | `->setPreviewMode(0\|1\|2\|CONST)` | `isObjectType(NodeTypeInterface)` | ✅ SAFE | 3538277 | 3538666 | Fixed |
| `ReplacePdoFetchConstantsRector` | `PDO::FETCH_*` constants | `isName(PDO)` on const fetch | ✅ SAFE | 3525077 | — | |
| `ReplaceRecipeRunnerInstallModuleRector` | `RecipeRunner::installModule()` static | `isName(RecipeRunner)` | ✅ SAFE | 3498026 | 3579527 | |
| `ReplaceSessionManagerDeleteRector` | `->delete()` | `isObjectType(SessionManager)` | ✅ SAFE | 3577376 | — | |
| `ReplaceSessionWritesWithRequestSessionRector` | `$_SESSION[...]` superglobal | n/a | ✅ SAFE | 3518527 | 3518914 | Specific superglobal |
| `ReplaceSystemPerformanceGzipKeyRector` | `->get()`/`->set()` on config chain | chain inspection for `'system.performance'` key | ✅ SAFE | 3184242 | 3526344 | Custom chain guard |
| `ReplaceThemeGetSettingRector` | `theme_get_setting()` etc. | n/a | ✅ SAFE | 3573896 | — | Global functions |
| `ReplaceUserSessionNamePropertyRector` | `->name` property | `isObjectType(UserSession)` + skips `$this` | ✅ SAFE | 3513856 | 3513877 | |
| `ReplaceViewsProceduralFunctionsRector` | `views_*()` functions | n/a | ✅ SAFE | 3572243 | 3572594 | Global functions |
| `StatementPrefetchIteratorFetchColumnRector` | `->fetchColumn()` | `isObjectType(StatementPrefetchIterator)` | ✅ SAFE | 3490200 | 3490312 | Fixed |
| `StripMigrationDependenciesExpandArgRector` | `->getMigrationDependencies()` | `isObjectType(MigrationInterface)` | ✅ SAFE | 3574717 | 3442785 | |
| `UseEntityTypeHasIntegerIdRector` | `$this->getEntityTypeIdKeyType()` etc. | `isObjectType` per-method via `METHOD_OWNER_CLASS` map | ✅ SAFE | 3566801 | 3566814 | Fixed |
| `ViewsPluginHandlerManagerRector` | `Views::pluginManager()` static | `isName(Views)` | ✅ SAFE | 3566424 | 3566982 | |
| `FunctionCallRemovalRector` (generic) | configured function names | configuration-driven name match | ✅ SAFE | — | — | Targets global functions only |

---

## AT-RISK Summary

All 12 AT-RISK rectors have been fixed. ✅

| # | Rector | Guard added | Drupal class/interface used | Issue | Change Record |
|---|--------|------------|------------------------------|-------|---------------|
| 1 | `ReplaceRebuildThemeDataRector` | `isObjectType` on `->rebuildThemeData()` caller | `Drupal\Core\Extension\ThemeHandlerInterface` | 3571068 | — |
| 2 | `PluginBaseIsConfigurableRector` | `isObjectType` on `$this` | `Drupal\Component\Plugin\PluginBase` | 3459533 | 3459535 |
| 3 | `RemoveConfigSaveTrustedDataArgRector` | `isObjectType` on `->save()` caller | `Drupal\Core\Config\Config` | 3347842 | 3348180 |
| 4 | `RemoveSetUriCallbackRector` | `isObjectType` on `->setUriCallback()` caller | `Drupal\Core\Entity\EntityTypeInterface` | 2667040 | 3575062 |
| 5 | `RemoveTrustDataCallRector` | `isObjectType` on `->trustData()` caller | `Drupal\Core\Config\Entity\ConfigEntityInterface` | 3347842 | 3348180 |
| 6 | `RemoveViewsRowCacheKeysRector` | `isObjectType` on method-call receiver inside array item | `Drupal\views\Plugin\views\cache\CachePluginBase` | 3564937 | 3564958 |
| 7 | `ReplaceEntityOriginalPropertyRector` | `isObjectType` on `->original` variable | `Drupal\Core\Entity\EntityInterface` | 3571065 | — |
| 8 | `ReplaceNodeSetPreviewModeRector` | `isObjectType` on `->setPreviewMode()` caller | `Drupal\node\NodeTypeInterface` | 3538277 | 3538666 |
| 9 | `StatementPrefetchIteratorFetchColumnRector` | `isObjectType` on `->fetchColumn()` caller | `Drupal\Core\Database\StatementPrefetchIterator` | 3490200 | 3490312 |
| 10 | `UseEntityTypeHasIntegerIdRector` | per-method `isObjectType` via `METHOD_OWNER_CLASS` map | `DefaultHtmlRouteProvider`, `CommentTypeForm`, `OverridesSectionStorage` | 3566801 | 3566814 |
| 11 | `LoadAllIncludesRector` | `isObjectType` on `->loadAllIncludes()` caller | `Drupal\Core\Extension\ModuleHandlerInterface` | 3536431 | 3536432 |
| 12 | `RemoveCacheExpireOverrideRector` | replaced broad `str_ends_with` with exact FQCN list; added `isObjectType(CachePluginBase)` fallback | `Drupal\views\Plugin\views\cache\CachePluginBase` | 3576556 | 3576855 |
