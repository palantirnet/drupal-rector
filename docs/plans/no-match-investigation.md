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

- [ ] `FileSystemBasenameToNativeRector` — modules: ejectorseat
- [ ] `LoadAllIncludesRector` — modules: config_track, schemadotorg
- [ ] `MigrateSqlGetMigrationPluginManagerRector` — modules: migmag, smart_sql_idmap
- [ ] `NodeStorageDeprecatedMethodsRector` — modules: tb_megamenu
- [ ] `PluginBaseIsConfigurableRector` — modules: metatag, search_api
- [ ] `RemoveModuleHandlerAddModuleCallsRector` — modules: acquia_contenthub, sdx
- [ ] `RemoveModuleHandlerDeprecatedMethodsRector` — modules: captcha, jsonld
- [ ] `RemoveSetUriCallbackRector` — modules: rabbit_hole_href
- [ ] `RemoveStateCacheSettingRector` — modules: searchstax, sdx
- [ ] `RemoveTwigNodeTransTagArgumentRector` — modules: searchstax
- [ ] `ReplaceAlphadecimalToIntNullRector` — modules: comment_mover
- [ ] `ReplaceCommentManagerGetCountNewCommentsRector` — modules: forum, history
- [ ] `ReplaceDateTimeRangeConstantsRector` — modules: deprecation_status
- [ ] `ReplaceEditorLoadRector` — modules: acquia_contenthub, ckeditor5_plugin_pack
- [ ] `ReplaceFieldgroupToFieldsetRector` — modules: field_group_vertical_tabs, ui_patterns_settings
- [ ] `ReplaceFileGetContentHeadersRector` — modules: commerce_invoice, tmgmt
- [ ] `ReplaceModuleHandlerGetNameRector` — modules: drd, reassign_user_content
- [ ] `ReplaceNodeAccessViewAllNodesRector` — modules: view_usernames_node_author
- [ ] `ReplaceNodeModuleProceduralFunctionsRector` — modules: reassign_user_content, addanother
- [ ] `ReplaceRecipeRunnerInstallModuleRector` — modules: schemadotorg
- [ ] `ReplaceSessionManagerDeleteRector` — modules: entity_visibility_preview, session_inspector
- [ ] `ReplaceSessionWritesWithRequestSessionRector` — modules: drd, entity_visibility_preview
- [ ] `ReplaceSystemPerformanceGzipKeyRector` — modules: drd
- [ ] `ReplaceUserSessionNamePropertyRector` — modules: acquia_contenthub, session_inspector
- [ ] `ViewsPluginHandlerManagerRector` — modules: searchstax, search_api, metatag
- [ ] `ReplaceRebuildThemeDataRector` — modules: site_guardian
- [ ] `ReplaceRequestTimeConstantRector` — modules: google_analytics_counter, automatic_updates
- [ ] `SystemTimeZonesRector` — modules: intl_date, smart_date
