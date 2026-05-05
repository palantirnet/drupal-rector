# Contrib Module Search — New Rectors

Use the Drupal GitLab search to find contrib modules that use the deprecated code each rector targets.

Base search URL (replace `QUERY` with the search term):
```
https://git.drupalcode.org/search?group_id=2&scope=blobs&search=-path%3Acore+-path%3Avendor+-path%3Adocroot+-path%3Aweb+-path%3Aprofiles+-path%3Asites+QUERY
```

---

## Drupal 11 Rectors

### ErrorCurrentErrorHandlerRector
- **Search:** `Error::currentErrorHandler`
- **Modules found:** —

### FileSystemBasenameToNativeRector
- **Search:** `->basename(`
- **Modules found:** —

### LoadAllIncludesRector
- **Search:** `->loadAllIncludes(`
- **Modules found:** —

### MigrateSqlGetMigrationPluginManagerRector
- **Search:** `->getMigrationPluginManager(`
- **Modules found:** —

### NodeStorageDeprecatedMethodsRector
- **Search:** `->revisionIds(` OR `->userRevisionIds(` OR `->countDefaultLanguageRevisions(`
- **Modules found:** —

### PluginBaseIsConfigurableRector
- **Search:** `->isConfigurable(`
- **Modules found:** —

### RemoveAutomatedCronSubmitHandlerRector
- **Search:** `automated_cron_settings_submit`
- **Modules found:** —

### RemoveCacheExpireOverrideRector
- **Search:** `function cacheExpire(`
- **Modules found:** —

### RemoveConfigSaveTrustedDataArgRector
- **Search:** `->save(TRUE)` or `->save(true)` on Config objects
- **Modules found:** —

### RemoveHandlerBaseDefineExtraOptionsRector
- **Search:** `function defineExtraOptions(`
- **Modules found:** —

### RemoveLinkWidgetValidateTitleElementRector
- **Search:** `LinkWidget::validateTitleElement`
- **Modules found:** —

### RemoveModuleHandlerAddModuleCallsRector
- **Search:** `->addModule(` OR `->addProfile(`
- **Modules found:** —

### RemoveModuleHandlerDeprecatedMethodsRector
- **Search:** `->writeCache(` OR `->getHookInfo(`
- **Modules found:** —

### RemoveRootFromConvertDbUrlRector
- **Search:** `convertDbUrlToConnectionInfo(`
- **Modules found:** —

### RemoveSetUriCallbackRector
- **Search:** `->setUriCallback(`
- **Modules found:** —

### RemoveStateCacheSettingRector
- **Search:** `state_cache`
- **Modules found:** —

### RemoveTrustDataCallRector
- **Search:** `->trustData(`
- **Modules found:** —

### RemoveTwigNodeTransTagArgumentRector
- **Search:** `TwigNodeTrans`
- **Modules found:** —

### RemoveUpdaterPostInstallMethodsRector
- **Search:** `function postInstall(` OR `function postInstallTasks(`
- **Modules found:** —

### RemoveViewsRowCacheKeysRector
- **Search:** `function getRowCacheKeys(` OR `function getRowId(`
- **Modules found:** —

### RenameStopProceduralHookScanRector
- **Search:** `StopProceduralHookScan`
- **Modules found:** —

### ReplaceAlphadecimalToIntNullRector
- **Search:** `alphadecimalToInt(`
- **Modules found:** —

### ReplaceCommentManagerGetCountNewCommentsRector
- **Search:** `->getCountNewComments(`
- **Modules found:** —

### ReplaceCommentUriRector
- **Search:** `comment_uri(`
- **Modules found:** —

### ReplaceDateTimeRangeConstantsRector
- **Search:** `DateTimeRangeConstantsInterface` OR `datetime_type_field_views_data_helper(`
- **Modules found:** —

### ReplaceEditorLoadRector
- **Search:** `editor_load(`
- **Modules found:** —

### ReplaceEntityOriginalPropertyRector
- **Search:** `->original`
- **Modules found:** —

### ReplaceEntityReferenceRecursiveLimitRector
- **Search:** `RECURSIVE_RENDER_LIMIT`
- **Modules found:** —

### ReplaceFieldgroupToFieldsetRector
- **Search:** `'#type' => 'fieldgroup'`
- **Modules found:** —

### ReplaceFileGetContentHeadersRector
- **Search:** `file_get_content_headers(`
- **Modules found:** —

### ReplaceLocaleConfigBatchFunctionsRector
- **Search:** `locale_config_batch_set_config_langcodes(` OR `locale_config_batch_refresh_name(`
- **Modules found:** —

### ReplaceNodeAccessViewAllNodesRector
- **Search:** `node_access_view_all_nodes(`
- **Modules found:** —

### ReplaceNodeAddBodyFieldRector
- **Search:** `node_add_body_field(`
- **Modules found:** —

### ReplaceNodeModuleProceduralFunctionsRector
- **Search:** `node_type_get_names(` OR `node_get_type_label(` OR `node_mass_update(`
- **Modules found:** —

### ReplaceNodeSetPreviewModeRector
- **Search:** `->setPreviewMode(`
- **Modules found:** —

### ReplacePdoFetchConstantsRector
- **Search:** `PDO::FETCH_`
- **Modules found:** —

### ReplaceRecipeRunnerInstallModuleRector
- **Search:** `RecipeRunner::installModule(`
- **Modules found:** —

### ReplaceSessionManagerDeleteRector
- **Search:** `SessionManager` + `->delete(`
- **Modules found:** —

### ReplaceSessionWritesWithRequestSessionRector
- **Search:** `$_SESSION[`
- **Modules found:** —

### ReplaceSystemPerformanceGzipKeyRector
- **Search:** `css.gzip` OR `js.gzip`
- **Modules found:** —

### ReplaceThemeGetSettingRector
- **Search:** `theme_get_setting(` OR `_system_default_theme_features(`
- **Modules found:** —

### ReplaceUserSessionNamePropertyRector
- **Search:** `->name` on UserSession (hard to search uniquely)
- **Modules found:** —

### ReplaceViewsProceduralFunctionsRector
- **Search:** `views_view_is_enabled(` OR `views_view_is_disabled(` OR `views_enable_view(` OR `views_disable_view(` OR `views_get_view_result(`
- **Modules found:** —

### StatementPrefetchIteratorFetchColumnRector
- **Search:** `->fetchColumn(`
- **Modules found:** —

### StripMigrationDependenciesExpandArgRector
- **Search:** `->getMigrationDependencies(`
- **Modules found:** —

### UseEntityTypeHasIntegerIdRector
- **Search:** `->getEntityTypeIdKeyType(` OR `->entityTypeSupportsComments(`
- **Modules found:** —

### ViewsPluginHandlerManagerRector
- **Search:** `Views::pluginManager(` OR `Views::handlerManager(`
- **Modules found:** —

---

## Drupal 10 Rectors

### ReplaceModuleHandlerGetNameRector
- **Search:** `->getName(` (on module handler — hard to search uniquely)
- **Modules found:** —

### ReplaceRebuildThemeDataRector
- **Search:** `->rebuildThemeData(`
- **Modules found:** —

### ReplaceRequestTimeConstantRector
- **Search:** `REQUEST_TIME`
- **Modules found:** —

### SystemTimeZonesRector (Drupal10)
- **Search:** `system_time_zones(`
- **Modules found:** —
