# Drupal Digest → Drupal Rector Comparison Report (Fresh Digest)

Compares each rector added in branch `feature/digest-rectors` against its source in
[drupal-digest-fresh](https://github.com/dbuytaert/drupal-digests) (regenerated with improved prompt).

**Total rectors compared:** 50
**Significant changes:** 28
**Minimal changes:** 22
**Split from one digest file:** 1 pair (`RemoveTrustDataCallRector` + `RemoveConfigSaveTrustedDataArgRector`)

---

## Overview Table

> Paths are relative to each repo root. Digest paths are relative to `drupal-digest-fresh/`.
> `†` = rector `@see` issue number differs from the digest filename — see [Notes](#notes-on-digest-file-mapping).

| Rector | Ver | Changes | Issue | Digest source | Rector destination |
|---|---|---|---|---|---|
| `ReplaceModuleHandlerGetNameRector` | D10 | **Significant** | [#3571063](https://www.drupal.org/node/3571063) | `rector/rules/replace-removed-modulehandlerinterface-getname-with-3571063.php` | `src/Drupal10/Rector/Deprecation/ReplaceModuleHandlerGetNameRector.php` |
| `ReplaceRebuildThemeDataRector` | D10 | **Significant** | [#3571068](https://www.drupal.org/node/3571068) | `rector/rules/replace-removed-themehandlerinterface-rebuildthemedata-with-3571068.php` | `src/Drupal10/Rector/Deprecation/ReplaceRebuildThemeDataRector.php` |
| `ReplaceRequestTimeConstantRector` | D10 | **Significant** | [#3395986](https://www.drupal.org/node/3395986) | `rector/rules/add-timeinterface-time-argument-to-plugin-constructor-3395986.php` | `src/Drupal10/Rector/Deprecation/ReplaceRequestTimeConstantRector.php` |
| `ErrorCurrentErrorHandlerRector` | D11 | Minimal | [#3526515](https://www.drupal.org/node/3526515) | `rector/rules/replace-error-currenterrorhandler-with-get-error-handler-3526515.php` | `src/Drupal11/Rector/Deprecation/ErrorCurrentErrorHandlerRector.php` |
| `FileSystemBasenameToNativeRector` | D11 | Minimal | [#3530461](https://www.drupal.org/node/3530461) | `rector/rules/replace-filesysteminterface-basename-with-native-basename-3530461.php` | `src/Drupal11/Rector/Deprecation/FileSystemBasenameToNativeRector.php` |
| `LoadAllIncludesRector` | D11 | **Significant** | [#3536431](https://www.drupal.org/node/3536431) | `rector/rules/replace-deprecated-modulehandler-loadallincludes-with-3536431.php` | `src/Drupal11/Rector/Deprecation/LoadAllIncludesRector.php` |
| `MigrateSqlGetMigrationPluginManagerRector` | D11 | **Significant** | [#3439369](https://www.drupal.org/node/3439369) | `rector/rules/replace-deprecated-sql-getmigrationpluginmanager-with-3439369.php` | `src/Drupal11/Rector/Deprecation/MigrateSqlGetMigrationPluginManagerRector.php` |
| `NodeStorageDeprecatedMethodsRector` | D11 | **Significant** | [#3396062](https://www.drupal.org/node/3396062) | `rector/rules/replace-deprecated-nodestorage-revisionids-and-3396062.php` | `src/Drupal11/Rector/Deprecation/NodeStorageDeprecatedMethodsRector.php` |
| `PluginBaseIsConfigurableRector` | D11 | **Significant** | [#3459533](https://www.drupal.org/node/3459533) | `rector/rules/replace-deprecated-pluginbase-isconfigurable-with-3459533.php` | `src/Drupal11/Rector/Deprecation/PluginBaseIsConfigurableRector.php` |
| `RemoveAutomatedCronSubmitHandlerRector` | D11 | **Significant** | [#3566768](https://www.drupal.org/node/3566768) | `rector/rules/remove-deprecated-automated-cron-settings-submit-calls-and-3566768.php` | `src/Drupal11/Rector/Deprecation/RemoveAutomatedCronSubmitHandlerRector.php` |
| `RemoveCacheExpireOverrideRector` | D11 | **Significant** | [#3576556](https://www.drupal.org/node/3576556) | `rector/rules/remove-deprecated-cacheexpire-overrides-from-views-3576556.php` | `src/Drupal11/Rector/Deprecation/RemoveCacheExpireOverrideRector.php` |
| `RemoveConfigSaveTrustedDataArgRector` | D11 | **Significant** | [#3347842](https://www.drupal.org/node/3347842) | `rector/rules/remove-deprecated-trusted-data-concept-from-drupal-config-3347842.php` *(split)* | `src/Drupal11/Rector/Deprecation/RemoveConfigSaveTrustedDataArgRector.php` |
| `RemoveHandlerBaseDefineExtraOptionsRector` | D11 | **Significant** | [#3485084](https://www.drupal.org/node/3485084) | `rector/rules/remove-overrides-of-deprecated-handlerbase-3485084.php` | `src/Drupal11/Rector/Deprecation/RemoveHandlerBaseDefineExtraOptionsRector.php` |
| `RemoveLinkWidgetValidateTitleElementRector` | D11 | Minimal | [#3093118](https://www.drupal.org/node/3093118) | `rector/rules/remove-deprecated-linkwidget-validatetitleelement-calls-3093118.php` | `src/Drupal11/Rector/Deprecation/RemoveLinkWidgetValidateTitleElementRector.php` |
| `RemoveModuleHandlerAddModuleCallsRector` | D11 | **Significant** | [#3528899](https://www.drupal.org/node/3528899) | `rector/rules/remove-deprecated-modulehandlerinterface-addmodule-and-3528899.php` | `src/Drupal11/Rector/Deprecation/RemoveModuleHandlerAddModuleCallsRector.php` |
| `RemoveModuleHandlerDeprecatedMethodsRector` | D11 | **Significant** | [#3442009](https://www.drupal.org/node/3442009) | `rector/rules/remove-deprecated-modulehandlerinterface-writecache-and-3442009.php` | `src/Drupal11/Rector/Deprecation/RemoveModuleHandlerDeprecatedMethodsRector.php` |
| `RemoveRootFromConvertDbUrlRector` | D11 | **Significant** | [#3522513](https://www.drupal.org/node/3522513) | `rector/rules/remove-deprecated-string-root-from-database-3522513.php` | `src/Drupal11/Rector/Deprecation/RemoveRootFromConvertDbUrlRector.php` |
| `RemoveSetUriCallbackRector` | D11 | **Significant** | [#2667040](https://www.drupal.org/node/2667040) | `rector/rules/remove-deprecated-entitytypeinterface-seturicallback-calls-2667040.php` | `src/Drupal11/Rector/Deprecation/RemoveSetUriCallbackRector.php` |
| `RemoveStateCacheSettingRector` | D11 | Minimal | [#3436954](https://www.drupal.org/node/3436954) | `rector/rules/remove-deprecated-settings-state-cache-assignment-3436954.php` | `src/Drupal11/Rector/Deprecation/RemoveStateCacheSettingRector.php` |
| `RemoveTrustDataCallRector` | D11 | **Significant** | [#3347842](https://www.drupal.org/node/3347842) | `rector/rules/remove-deprecated-trusted-data-concept-from-drupal-config-3347842.php` *(split)* | `src/Drupal11/Rector/Deprecation/RemoveTrustDataCallRector.php` |
| `RemoveTwigNodeTransTagArgumentRector` | D11 | **Significant** | [#3473440](https://www.drupal.org/node/3473440) | `rector/rules/remove-deprecated-tag-argument-from-twignodetrans-3473440.php` | `src/Drupal11/Rector/Deprecation/RemoveTwigNodeTransTagArgumentRector.php` |
| `RemoveUpdaterPostInstallMethodsRector` | D11 | Minimal | [#3417136](https://www.drupal.org/node/3417136) | `rector/rules/remove-deprecated-updater-postinstall-postinstalltasks-3417136.php` | `src/Drupal11/Rector/Deprecation/RemoveUpdaterPostInstallMethodsRector.php` |
| `RemoveViewsRowCacheKeysRector` | D11 | **Significant** | [#3564958](https://www.drupal.org/node/3564958) † | `rector/rules/remove-deprecated-cachepluginbase-getrowcachekeys-and-3564937.php` | `src/Drupal11/Rector/Deprecation/RemoveViewsRowCacheKeysRector.php` |
| `RenameStopProceduralHookScanRector` | D11 | **Significant** | [#3495943](https://www.drupal.org/node/3495943) | `rector/rules/rename-stopproceduralhookscan-attribute-to-3495943.php` | `src/Drupal11/Rector/Deprecation/RenameStopProceduralHookScanRector.php` |
| `ReplaceAlphadecimalToIntNullRector` | D11 | Minimal | [#3442810](https://www.drupal.org/node/3442810) | `rector/rules/replace-deprecated-number-alphadecimaltoint-null-calls-with-3442810.php` | `src/Drupal11/Rector/Deprecation/ReplaceAlphadecimalToIntNullRector.php` |
| `ReplaceCommentManagerGetCountNewCommentsRector` | D11 | **Significant** | [#3551729](https://www.drupal.org/node/3551729) † | `rector/rules/replace-deprecated-commentmanagerinterface-3543035.php` | `src/Drupal11/Rector/Deprecation/ReplaceCommentManagerGetCountNewCommentsRector.php` |
| `ReplaceCommentUriRector` | D11 | Minimal | [#2010202](https://www.drupal.org/node/2010202) | `rector/rules/replace-deprecated-comment-uri-with-comment-permalink-2010202.php` | `src/Drupal11/Rector/Deprecation/ReplaceCommentUriRector.php` |
| `ReplaceDateTimeRangeConstantsRector` | D11 | Minimal | [#3574901](https://www.drupal.org/node/3574901) | `rector/rules/replace-removed-datetimerangeconstantsinterface-constants-3574901.php` | `src/Drupal11/Rector/Deprecation/ReplaceDateTimeRangeConstantsRector.php` |
| `ReplaceEditorLoadRector` | D11 | **Significant** | [#3447794](https://www.drupal.org/node/3447794) | `rector/rules/replace-deprecated-editor-load-with-entity-storage-load-3447794.php` | `src/Drupal11/Rector/Deprecation/ReplaceEditorLoadRector.php` |
| `ReplaceEntityOriginalPropertyRector` | D11 | **Significant** | [#3571065](https://www.drupal.org/node/3571065) | `rector/rules/replace-deprecated-entity-original-magic-property-with-3571065.php` | `src/Drupal11/Rector/Deprecation/ReplaceEntityOriginalPropertyRector.php` |
| `ReplaceEntityReferenceRecursiveLimitRector` | D11 | Minimal | [#3316878](https://www.drupal.org/node/3316878) † | `rector/rules/replace-deprecated-entityreferenceentityformatter-recursive-2940605.php` | `src/Drupal11/Rector/Deprecation/ReplaceEntityReferenceRecursiveLimitRector.php` |
| `ReplaceFieldgroupToFieldsetRector` | D11 | Minimal | [#3512254](https://www.drupal.org/node/3512254) | `rector/rules/replace-deprecated-type-fieldgroup-with-type-fieldset-3512254.php` | `src/Drupal11/Rector/Deprecation/ReplaceFieldgroupToFieldsetRector.php` |
| `ReplaceFileGetContentHeadersRector` | D11 | Minimal | [#3494126](https://www.drupal.org/node/3494126) | `rector/rules/replace-file-get-content-headers-with-fileinterface-3494126.php` | `src/Drupal11/Rector/Deprecation/ReplaceFileGetContentHeadersRector.php` |
| `ReplaceLocaleConfigBatchFunctionsRector` | D11 | **Significant** | [#3575254](https://www.drupal.org/node/3575254) | `rector/rules/replace-deprecated-locale-batch-functions-with-their-3575254.php` | `src/Drupal11/Rector/Deprecation/ReplaceLocaleConfigBatchFunctionsRector.php` |
| `ReplaceNodeAccessViewAllNodesRector` | D11 | Minimal | [#3038908](https://www.drupal.org/node/3038908) | `rector/rules/replace-deprecated-node-access-view-all-nodes-with-oo-3038908.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeAccessViewAllNodesRector.php` |
| `ReplaceNodeAddBodyFieldRector` | D11 | Minimal | [#3489266](https://www.drupal.org/node/3489266) | `rector/rules/replace-deprecated-node-add-body-field-with-createbodyfield-3489266.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeAddBodyFieldRector.php` |
| `ReplaceNodeModuleProceduralFunctionsRector` | D11 | Minimal | [#3571623](https://www.drupal.org/node/3571623) | `rector/rules/replace-deprecated-node-module-procedural-functions-with-oo-3571623.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeModuleProceduralFunctionsRector.php` |
| `ReplaceNodeSetPreviewModeRector` | D11 | **Significant** | [#3538277](https://www.drupal.org/node/3538277) | `rector/rules/replace-deprecated-constants-with-nodepreviewmode-enum-in-3538277.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeSetPreviewModeRector.php` |
| `ReplacePdoFetchConstantsRector` | D11 | **Significant** | [#3525077](https://www.drupal.org/node/3525077) | `rector/rules/replace-removed-mysql-pgsql-sqlite-driver-query-subclass-3525077.php` | `src/Drupal11/Rector/Deprecation/ReplacePdoFetchConstantsRector.php` |
| `ReplaceRecipeRunnerInstallModuleRector` | D11 | Minimal | [#3498026](https://www.drupal.org/node/3498026) | `rector/rules/replace-deprecated-reciperunner-installmodule-with-3498026.php` | `src/Drupal11/Rector/Deprecation/ReplaceRecipeRunnerInstallModuleRector.php` |
| `ReplaceSessionManagerDeleteRector` | D11 | **Significant** | [#3577376](https://www.drupal.org/node/3577376) | `rector/rules/replace-deprecated-sessionmanager-delete-with-3577376.php` | `src/Drupal11/Rector/Deprecation/ReplaceSessionManagerDeleteRector.php` |
| `ReplaceSessionWritesWithRequestSessionRector` | D11 | Minimal | [#3518527](https://www.drupal.org/node/3518527) | `rector/rules/replace-deprecated-session-writes-with-drupal-request-3518527.php` | `src/Drupal11/Rector/Deprecation/ReplaceSessionWritesWithRequestSessionRector.php` |
| `ReplaceSystemPerformanceGzipKeyRector` | D11 | Minimal | [#3184242](https://www.drupal.org/node/3184242) | `rector/rules/replace-deprecated-system-performance-css-gzip-js-gzip-3184242.php` | `src/Drupal11/Rector/Deprecation/ReplaceSystemPerformanceGzipKeyRector.php` |
| `ReplaceThemeGetSettingRector` | D11 | Minimal | [#3573896](https://www.drupal.org/node/3573896) | `rector/rules/replace-deprecated-theme-get-setting-and-system-default-3573896.php` | `src/Drupal11/Rector/Deprecation/ReplaceThemeGetSettingRector.php` |
| `ReplaceUserSessionNamePropertyRector` | D11 | Minimal | [#3513856](https://www.drupal.org/node/3513856) | `rector/rules/replace-deprecated-usersession-name-property-read-with-3513856.php` | `src/Drupal11/Rector/Deprecation/ReplaceUserSessionNamePropertyRector.php` |
| `ReplaceViewsProceduralFunctionsRector` | D11 | Minimal | [#3572243](https://www.drupal.org/node/3572243) | `rector/rules/replace-deprecated-views-procedural-functions-with-oo-3572243.php` | `src/Drupal11/Rector/Deprecation/ReplaceViewsProceduralFunctionsRector.php` |
| `StatementPrefetchIteratorFetchColumnRector` | D11 | **Significant** | [#3490200](https://www.drupal.org/node/3490200) | `rector/rules/replace-deprecated-statementprefetchiterator-fetchcolumn-3490200.php` | `src/Drupal11/Rector/Deprecation/StatementPrefetchIteratorFetchColumnRector.php` |
| `StripMigrationDependenciesExpandArgRector` | D11 | Minimal | [#3574717](https://www.drupal.org/node/3574717) | `rector/rules/strip-removed-expand-argument-from-getmigrationdependencies-3574717.php` | `src/Drupal11/Rector/Deprecation/StripMigrationDependenciesExpandArgRector.php` |
| `UseEntityTypeHasIntegerIdRector` | D11 | **Significant** | [#3566801](https://www.drupal.org/node/3566801) | `rector/rules/replace-deprecated-entity-type-integer-id-helpers-with-3566801.php` | `src/Drupal11/Rector/Deprecation/UseEntityTypeHasIntegerIdRector.php` |
| `ViewsPluginHandlerManagerRector` | D11 | Minimal | [#3566424](https://www.drupal.org/node/3566424) | `rector/rules/replace-deprecated-views-pluginmanager-and-views-3566424.php` | `src/Drupal11/Rector/Deprecation/ViewsPluginHandlerManagerRector.php` |

---

## Significant Changes

### 1. `ReplaceModuleHandlerGetNameRector` (Drupal10)

**Digest file:** `replace-removed-modulehandlerinterface-getname-with-3571063.php`

**Change:** The fresh digest used a plain `AbstractRector` with a simple `refactor()` method. The rector integrates into the `AbstractDrupalCoreRector` framework with `DrupalIntroducedVersionConfiguration('10.3.0')`, using `refactorWithConfiguration()` and `ConfiguredCodeSample` in `getRuleDefinition()`. The transformation logic is otherwise identical.

---

### 2. `ReplaceRebuildThemeDataRector` (Drupal10)

**Digest file:** `replace-removed-themehandlerinterface-rebuildthemedata-with-3571068.php`

**Change:** Like `ReplaceModuleHandlerGetNameRector`, the rector wraps the logic in `AbstractDrupalCoreRector` with `DrupalIntroducedVersionConfiguration('10.3.0')`. Additionally, the rector adds a `ThemeHandlerInterface` ObjectType check that was missing from the fresh digest.

```php
// Fresh digest — no type guard
if (!$this->isName($node->name, 'rebuildThemeData')) {
    return null;
}

// Rector — adds ThemeHandlerInterface type guard
if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Extension\ThemeHandlerInterface'))) {
    return null;
}
```

---

### 3. `ReplaceRequestTimeConstantRector` (Drupal10)

**Digest file:** `add-timeinterface-time-argument-to-plugin-constructor-3395986.php`

**Change:** The issue ID is shared but the two rules address entirely different deprecations. The fresh digest adds a `?TimeInterface $time` argument to `__construct()` overrides across six Drupal plugin parent classes. The rector instead replaces the `REQUEST_TIME` constant with `\Drupal::time()->getRequestTime()`. No code from the digest was reused — the rector is a completely independent implementation.

---

### 4. `LoadAllIncludesRector` (Drupal11)

**Digest file:** `replace-deprecated-modulehandler-loadallincludes-with-3536431.php`

**Change:** The fresh digest had no type guard — it rewrote any `loadAllIncludes()` call regardless of object type. The rector adds a `ModuleHandlerInterface` ObjectType check before rewriting.

```php
// Fresh digest — no type guard
if (!$this->isName($methodCall->name, 'loadAllIncludes')) {
    return null;
}

// Rector — requires ModuleHandlerInterface
if (!$this->isObjectType($methodCall->var, new ObjectType('Drupal\Core\Extension\ModuleHandlerInterface'))) {
    return null;
}
```

---

### 5. `MigrateSqlGetMigrationPluginManagerRector` (Drupal11)

**Digest file:** `replace-deprecated-sql-getmigrationpluginmanager-with-3439369.php`

**Change:** The type-check approach is inverted. The fresh digest used a negative guard — skip if the caller is a `Migration` instance, allowing any other caller through. The rector uses a positive guard — only proceed if the caller is specifically a `Sql` instance. The rector's approach is more restrictive and precise.

```php
// Fresh digest — negative guard (exclude Migration)
if ($this->isObjectType($node->var, new ObjectType('Drupal\migrate\Plugin\Migration'))) {
    return null;
}

// Rector — positive guard (require Sql)
if (!$this->isObjectType($node->var, new ObjectType('Drupal\migrate\Plugin\migrate\id_map\Sql'))) {
    return null;
}
```

---

### 6. `NodeStorageDeprecatedMethodsRector` (Drupal11)

**Digest file:** `replace-deprecated-nodestorage-revisionids-and-3396062.php`

**Change:** The fresh digest handled only `revisionIds()` and `userRevisionIds()`. The rector adds handling for `countDefaultLanguageRevisions()`, which has no replacement and must be removed entirely. This requires registering `Expression::class` as an additional node type and using `NodeVisitor::REMOVE_NODE`.

```php
// Rector — additional node type and removal handling
public function getNodeTypes(): array
{
    return [Node\Expr\MethodCall::class, Node\Stmt\Expression::class];
}

// Removes countDefaultLanguageRevisions() entirely
if ($node instanceof Node\Stmt\Expression) {
    if ($this->getName($methodCall->name) === 'countDefaultLanguageRevisions') {
        return NodeVisitor::REMOVE_NODE;
    }
}
```

---

### 7. `PluginBaseIsConfigurableRector` (Drupal11)

**Digest file:** `replace-deprecated-pluginbase-isconfigurable-with-3459533.php`

**Change:** The fresh digest relied solely on detecting `$this->isConfigurable()` (variable named `this`, no args) without any type guard. The rector adds an explicit `isObjectType($node->var, new ObjectType('Drupal\Component\Plugin\PluginBase'))` check, preventing false positives on any other class that may have an `isConfigurable()` method.

```php
// Fresh digest — $this check only, no type guard
if ($this->getName($node->var) !== 'this') {
    return null;
}

// Rector — adds PluginBase type guard
if ($this->getName($node->var) !== 'this') {
    return null;
}
if (!$this->isObjectType($node->var, new ObjectType('Drupal\Component\Plugin\PluginBase'))) {
    return null;
}
```

---

### 8. `RemoveAutomatedCronSubmitHandlerRector` (Drupal11)

**Digest file:** `remove-deprecated-automated-cron-settings-submit-calls-and-3566768.php`

**Change:** The fresh digest registered two rules: a custom class for `$form['#submit'][]` array-append removal, and `RemoveFuncCallRector` (a built-in Rector rule) for direct `automated_cron_settings_submit()` function calls. The rector implements only the array-append removal, omitting the direct function-call case. The class was also renamed from `RemoveAutomatedCronSettingsSubmitHandlerRector` to `RemoveAutomatedCronSubmitHandlerRector`.

---

### 9. `RemoveCacheExpireOverrideRector` (Drupal11)

**Digest file:** `remove-deprecated-cacheexpire-overrides-from-views-3576556.php`

**Change:** The rector significantly improves the class-hierarchy detection logic. It adds a `PARENT_FQCNS` constant listing all four known fully-qualified parent class names (`CachePluginBase`, `Time`, `Tag`, `None`), adds `'None'` to `PARENT_SHORT_NAMES`, and uses `str_ends_with($parentName, '\\' . $short)` for namespace-relative names to prevent false matches on partial namespace strings.

```php
// Rector — adds PARENT_FQCNS constant
private const PARENT_FQCNS = [
    'Drupal\views\Plugin\views\cache\CachePluginBase',
    'Drupal\views\Plugin\views\cache\Time',
    'Drupal\views\Plugin\views\cache\Tag',
    'Drupal\views\Plugin\views\cache\None',
];
// Matches FQCNs first, then restricts short-name check to unqualified names
if (!str_contains($parentName, '\\')) {
    foreach (self::PARENT_SHORT_NAMES as $short) {
```

---

### 10. `RemoveConfigSaveTrustedDataArgRector` + `RemoveTrustDataCallRector` (Drupal11)

**Digest file:** `remove-deprecated-trusted-data-concept-from-drupal-config-3347842.php` (one file, split into two rector classes)

**Change:** The fresh digest defined a single class covering both patterns. The rector splits them into two focused files. `RemoveConfigSaveTrustedDataArgRector` handles only `Config::save(TRUE/FALSE)` and adds a `Drupal\Core\Config\Config` ObjectType check. `RemoveTrustDataCallRector` handles only `->trustData()` chain removal and adds a `ConfigEntityInterface` ObjectType check. Both additions prevent false positives that the combined digest class was susceptible to.

```php
// RemoveConfigSaveTrustedDataArgRector — adds Config type guard
if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Config\Config'))) {
    return null;
}

// RemoveTrustDataCallRector — adds ConfigEntityInterface type guard
if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Config\Entity\ConfigEntityInterface'))) {
    return null;
}
```

---

### 11. `RemoveHandlerBaseDefineExtraOptionsRector` (Drupal11)

**Digest file:** `remove-overrides-of-deprecated-handlerbase-3485084.php`

**Change:** The fresh digest's `PARENT_SHORT_NAMES` covered only `HandlerBase`. The rector expands it to cover five additional handler base classes: `FieldHandlerBase`, `FilterPluginBase`, `SortPluginBase`, `ArgumentPluginBase`, and `RelationshipPluginBase`. The rector also adds an `isObjectType` PHPStan fallback and uses a different approach for the exclusion of the `HandlerBase` class itself.

```php
// Fresh digest
private const PARENT_SHORT_NAMES = ['HandlerBase'];

// Rector
private const PARENT_SHORT_NAMES = [
    'HandlerBase',
    'FieldHandlerBase',
    'FilterPluginBase',
    'SortPluginBase',
    'ArgumentPluginBase',
    'RelationshipPluginBase',
];
```

---

### 12. `RemoveModuleHandlerAddModuleCallsRector` (Drupal11)

**Digest file:** `remove-deprecated-modulehandlerinterface-addmodule-and-3528899.php`

**Change:** The fresh digest checked only `ModuleHandlerInterface`. The rector also checks the concrete `ModuleHandler` class, covering cases where the variable is typed as the implementation rather than the interface.

```php
// Fresh digest — interface only
if ($this->isObjectType($methodCall->var, new ObjectType('Drupal\\Core\\Extension\\ModuleHandlerInterface'))) {

// Rector — interface + concrete class
foreach (['Drupal\Core\Extension\ModuleHandlerInterface', 'Drupal\Core\Extension\ModuleHandler'] as $class) {
    if ($this->isObjectType($methodCall->var, new ObjectType($class))) {
        $isModuleHandler = true;
        break;
    }
}
```

---

### 13. `RemoveModuleHandlerDeprecatedMethodsRector` (Drupal11)

**Digest file:** `remove-deprecated-modulehandlerinterface-writecache-and-3442009.php`

**Change:** Both rules remove `writeCache()` and replace `getHookInfo()` with `[]`. The rector goes further: it removes standalone `getHookInfo()` expression statements entirely (via `NodeVisitor::REMOVE_NODE`) rather than leaving bare `[];` statements behind, as the digest did. The rector also refactors detection into a private `isModuleHandlerMethodCall()` helper.

---

### 14. `RemoveRootFromConvertDbUrlRector` (Drupal11)

**Digest file:** `remove-deprecated-string-root-from-database-3522513.php`

**Change:** The rector recognizes more expression types as valid second-argument forms to strip. It adds `StaticPropertyFetch` and `MethodCall` to the recognized node types (the fresh digest handled only `Variable`, `String_`, and `ClassConstFetch`). The class was also renamed from `RemoveRootFromConvertDbUrlToConnectionInfoRector`.

---

### 15. `RemoveSetUriCallbackRector` (Drupal11)

**Digest file:** `remove-deprecated-entitytypeinterface-seturicallback-calls-2667040.php`

**Change:** The fresh digest had no type guard — it removed any `setUriCallback()` call by method name alone. The rector adds `isObjectType($node->expr->var, new ObjectType('Drupal\Core\Entity\EntityTypeInterface'))` checks on both the standalone-statement case and the fluent-chain case.

```php
// Fresh digest — no type guard
if ($node->expr instanceof MethodCall && $this->isName($node->expr->name, 'setUriCallback')) {
    return NodeVisitor::REMOVE_NODE;
}

// Rector — type-guarded
if ($node->expr instanceof MethodCall
    && $this->isName($node->expr->name, 'setUriCallback')
    && $this->isObjectType($node->expr->var, new ObjectType('Drupal\Core\Entity\EntityTypeInterface'))
) {
    return NodeVisitor::REMOVE_NODE;
}
```

---

### 16. `RemoveTwigNodeTransTagArgumentRector` (Drupal11)

**Digest file:** `remove-deprecated-tag-argument-from-twignodetrans-3473440.php`

**Change:** The strategies for argument removal and class matching both differ. The rector checks `count($node->args) === 6` exactly and uses `array_pop()` to remove the last argument. The fresh digest used `isset($node->args[5])` and `array_splice($node->args, 5)`, which would also handle cases with more than six arguments. The rector additionally matches the short class name `TwigNodeTrans` (without namespace) in addition to the FQCN. The class was renamed from `RemoveTwigNodeTransTagArgRector`.

---

### 17. `RemoveViewsRowCacheKeysRector` (Drupal11)

**Digest file:** `remove-deprecated-cachepluginbase-getrowcachekeys-and-3564937.php`

**Note:** The rector's `@see` references issue `#3564958`; the digest file uses `#3564937`. Both numbers refer to the same deprecation — `3564958` is the change record and `3564937` is the original issue.

**Change:** The fresh digest removed array items whose value was a call to `getRowCacheKeys()` or `getRowId()` by method name alone, with no type guard. The rector adds `isObjectType($item->value->var, new ObjectType('Drupal\views\Plugin\views\cache\CachePluginBase'))`, preventing false positives when another class has methods with the same names.

```php
// Fresh digest — no type guard
if ($item->value instanceof MethodCall && $this->isDeprecatedMethodCall($item->value)) {

// Rector — type-guarded
if ($item->value instanceof MethodCall
    && $item->value->name instanceof Identifier
    && in_array($item->value->name->toString(), self::DEPRECATED_METHODS, true)
    && $this->isObjectType($item->value->var, new ObjectType('Drupal\views\Plugin\views\cache\CachePluginBase'))
) {
```

---

### 18. `RenameStopProceduralHookScanRector` (Drupal11)

**Digest file:** `rename-stopproceduralhookscan-attribute-to-3495943.php`

**Change:** The fresh digest was a trivial config snippet (two lines of real logic) using the built-in `RenameClassRector`. The rector implements a full custom rule visiting both `UseUse` and `Attribute` AST nodes to rename the use-statement and the attribute usage site independently, preserving correct formatting and avoiding the risk of `RenameClassRector` rewriting unrelated class body references.

---

### 19. `ReplaceCommentManagerGetCountNewCommentsRector` (Drupal11)

**Digest file:** `replace-deprecated-commentmanagerinterface-3543035.php`

**Note:** The rector's `@see` references issue `#3551729`; the digest file uses `#3543035`. Both reference the same deprecation — `#3543035` is the original issue, `#3551729` is the related change record.

**Change:** The fresh digest extended `AbstractRector` directly with a plain `refactor()` method. The rector extends `AbstractDrupalCoreRector` and wraps the logic in `refactorWithConfiguration()`, enabling version-gated activation via `DrupalIntroducedVersionConfiguration('11.3.0')`.

```php
// Fresh digest
final class CommentManagerGetCountNewCommentsRector extends AbstractRector
{
    public function refactor(Node $node): ?Node { ... }

// Rector
final class ReplaceCommentManagerGetCountNewCommentsRector extends AbstractDrupalCoreRector
{
    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node { ... }
    // getRuleDefinition() uses ConfiguredCodeSample with DrupalIntroducedVersionConfiguration('11.3.0')
```

---

### 20. `ReplaceEditorLoadRector` (Drupal11)

**Digest file:** `replace-deprecated-editor-load-with-entity-storage-load-3447794.php`

**Change:** The rector uses `$this->nodeFactory->createStaticCall()` and `createMethodCall()` helpers from Rector's `NodeFactory` for cleaner AST construction, replacing the fresh digest's inline manual node construction. The rector also adds a `count($node->args) !== 1` guard that the digest lacked. The class was renamed from `EditorLoadDeprecationRector`.

---

### 21. `ReplaceEntityOriginalPropertyRector` (Drupal11)

**Digest file:** `replace-deprecated-entity-original-magic-property-with-3571065.php`

**Change:** The fresh digest handled `PropertyFetch` and `Assign` nodes only, with no type guard on read accesses. The rector adds `NullsafePropertyFetch` as a third node type, rewriting `$entity?->original` to `$entity?->getOriginal()` via `NullsafeMethodCall`. It also adds an `EntityInterface` ObjectType check on the `PropertyFetch` branch. The class was renamed from `EntityOriginalPropertyToMethodRector`.

```php
// Fresh digest — only PropertyFetch and Assign
public function getNodeTypes(): array
{
    return [PropertyFetch::class, Assign::class];
}
// No isObjectType check on the PropertyFetch branch

// Rector — adds NullsafePropertyFetch and EntityInterface type guard
public function getNodeTypes(): array
{
    return [PropertyFetch::class, NullsafePropertyFetch::class, Assign::class];
}
if ($this->isObjectType($node->var, new ObjectType('Drupal\Core\Entity\EntityInterface'))) {
    return new MethodCall($node->var, 'getOriginal');
}
```

---

### 22. `ReplaceLocaleConfigBatchFunctionsRector` (Drupal11)

**Digest file:** `replace-deprecated-locale-batch-functions-with-their-3575254.php`

**Change:** The fresh digest was a config snippet using the built-in `RenameFunctionRector`. The rector implements a full custom `FuncCall`-visiting rule with a `RENAME_MAP` constant, providing type-safety, testability, and more control over the transformation.

---

### 23. `ReplaceNodeSetPreviewModeRector` (Drupal11)

**Digest file:** `replace-deprecated-constants-with-nodepreviewmode-enum-in-3538277.php`

**Change:** The fresh digest had no type guard on `setPreviewMode()` — it would rewrite the call on any object. The rector adds `isObjectType($node->var, new ObjectType('Drupal\node\NodeTypeInterface'))`, preventing false positives on unrelated classes with the same method name. The class was renamed from `NodeSetPreviewModeRector`.

```php
// Fresh digest — no type guard
if (!$this->isName($node->name, 'setPreviewMode')) { return null; }

// Rector — NodeTypeInterface guard
if (!$this->isObjectType($node->var, new ObjectType('Drupal\node\NodeTypeInterface'))) {
    return null;
}
```

---

### 24. `ReplacePdoFetchConstantsRector` (Drupal11)

**Digest file:** `replace-removed-mysql-pgsql-sqlite-driver-query-subclass-3525077.php`

**Change:** The issue ID is shared but the two rules address entirely different aspects of the same deprecation. The fresh digest was a config snippet using `RenameClassRector` to repoint nine deprecated driver-specific query subclasses to their `Drupal\Core\Database\Query\*` equivalents. The rector is a full custom rule converting `PDO::FETCH_*` constants to `FetchAs` enum cases across `setFetchMode()`, `fetch()`, `fetchAll()`, `fetchAllAssoc()`, and `'fetch'` array keys. No code from the digest was reused.

---

### 25. `ReplaceSessionManagerDeleteRector` (Drupal11)

**Digest file:** `replace-deprecated-sessionmanager-delete-with-3577376.php`

**Change:** The fresh digest extended `AbstractRector` directly with a plain `refactor()` method. The rector extends `AbstractDrupalCoreRector` and uses `refactorWithConfiguration()` with `DrupalIntroducedVersionConfiguration('11.4.0')`. The type-check strategy was also changed from the PHPStan `$sessionManagerType->isSuperTypeOf($callerType)->yes()` pattern to the standard Rector `$this->isObjectType()` API.

```php
// Fresh digest
final class ReplaceSessionManagerDeleteRector extends AbstractRector
{
    public function refactor(Node $node): ?Node { ... }

// Rector
final class ReplaceSessionManagerDeleteRector extends AbstractDrupalCoreRector
{
    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node { ... }
```

---

### 26. `StatementPrefetchIteratorFetchColumnRector` (Drupal11)

**Digest file:** `replace-deprecated-statementprefetchiterator-fetchcolumn-3490200.php`

**Change:** The fresh digest was a config snippet using `RenameMethodRector` to rename `fetchColumn` → `fetchField` on `StatementPrefetchIterator`. The rector implements a full custom `MethodCall`-visiting rule with an explicit `StatementPrefetchIterator` ObjectType check, making the transformation testable and precise.

---

### 27. `UseEntityTypeHasIntegerIdRector` (Drupal11)

**Digest file:** `replace-deprecated-entity-type-integer-id-helpers-with-3566801.php`

**Change:** The fresh digest treated `entityTypeSupportsComments()` and `hasIntegerId()` as simple `$this->method()` rewrites with no type guard — any class with those method names would be transformed. The rector adds a `METHOD_OWNER_CLASS` constant that maps each method to its declaring class FQCN and calls `isObjectType()` before transforming, preventing false positives entirely.

```php
// Fresh digest — no type guard
private const SIMPLE_METHOD_NAMES = ['entityTypeSupportsComments'];
if (in_array($methodName, self::SIMPLE_METHOD_NAMES, true) && count($node->args) === 1) {
    return new MethodCall($node->args[0]->value, 'hasIntegerId');
}

// Rector — type-guarded via METHOD_OWNER_CLASS
private const METHOD_OWNER_CLASS = [
    'entityTypeSupportsComments' => 'Drupal\comment\CommentTypeForm',
    'hasIntegerId' => 'Drupal\layout_builder\Plugin\SectionStorage\OverridesSectionStorage',
];
if (!$this->isObjectType($node->var, new ObjectType(self::METHOD_OWNER_CLASS[$name]))) {
    return null;
}
```

---

## Minimal Changes

These rectors are functionally identical to their fresh-digest counterparts. Differences are limited to: namespace declarations, proper `use` imports (replacing inline backslash-prefixed FQCNs), `declare(strict_types=1)` placement, class renaming to match drupal-rector naming conventions, and minor wording in `getRuleDefinition()`.

| Rector class | Notable structural differences from digest |
|---|---|
| `ErrorCurrentErrorHandlerRector` | Namespace + imports only; `ObjectType` imported vs inline `\PHPStan\Type\ObjectType` |
| `FileSystemBasenameToNativeRector` | Namespace + imports only; type-check API changed from `isSuperTypeOf()->yes()` to `isObjectType()` (semantically equivalent) |
| `RemoveLinkWidgetValidateTitleElementRector` | Namespace + imports only |
| `RemoveStateCacheSettingRector` | Namespace + imports only |
| `RemoveUpdaterPostInstallMethodsRector` | Namespace + imports only; backslash escaping in `UPDATER_BASE_CLASSES` normalized |
| `ReplaceAlphadecimalToIntNullRector` | Namespace + imports only; class renamed from `AlphadecimalToIntNullOrEmptyRector` |
| `ReplaceCommentUriRector` | Namespace + imports only; class renamed from `CommentUriToPermalinkRector`; arg count check changed from `!== 1` to `< 1` |
| `ReplaceDateTimeRangeConstantsRector` | Namespace + imports only; class renamed from `ReplaceDatetimeDeprecatedApisRector` |
| `ReplaceEntityReferenceRecursiveLimitRector` | Namespace + imports only; class name preserved; logic identical |
| `ReplaceFieldgroupToFieldsetRector` | Namespace + imports only; class renamed from `FieldgroupToFieldsetRector` |
| `ReplaceFileGetContentHeadersRector` | Namespace + imports only; class renamed from `FileGetContentHeadersRector` |
| `ReplaceNodeAccessViewAllNodesRector` | Namespace + imports only; class renamed from `NodeAccessViewAllNodesRector` |
| `ReplaceNodeAddBodyFieldRector` | Namespace + imports only; class renamed from `NodeAddBodyFieldRector` |
| `ReplaceNodeModuleProceduralFunctionsRector` | Namespace + imports only; class renamed from `ReplaceDeprecatedNodeFunctionsRector`; private constants replaced with inline strings |
| `ReplaceRecipeRunnerInstallModuleRector` | Namespace + imports only; class renamed from `RecipeRunnerInstallModuleRector` |
| `ReplaceSessionWritesWithRequestSessionRector` | Namespace + imports only; class renamed from `SessionSuperGlobalToRequestSessionRector` |
| `ReplaceSystemPerformanceGzipKeyRector` | Namespace + imports only; class renamed from `SystemPerformanceGzipToCompressRector` |
| `ReplaceThemeGetSettingRector` | Namespace + imports only |
| `ReplaceUserSessionNamePropertyRector` | Namespace + imports only; class renamed from `UserSessionNamePropertyToGetAccountNameRector`; adds `UserSession` ObjectType check |
| `ReplaceViewsProceduralFunctionsRector` | Namespace + imports only; class renamed from `ReplaceDeprecatedViewsFunctionsRector` |
| `StripMigrationDependenciesExpandArgRector` | Namespace + imports only; class renamed from `RemoveMigrationDependenciesExpandArgRector`; type-check API changed from `isSuperTypeOf()->yes()` to `isObjectType()` (semantically equivalent) |
| `ViewsPluginHandlerManagerRector` | Namespace + imports only; class-name check changed from `isObjectType()` to `isName()` (correct for static call class nodes) |

---

## Notes on Digest File Mapping

### One digest file → two rector files
`remove-deprecated-trusted-data-concept-from-drupal-config-3347842.php` defined two classes in a single file: a combined handler for both `save(TRUE)` and `->trustData()` patterns. The rector project splits these into two separate class files — `RemoveConfigSaveTrustedDataArgRector` and `RemoveTrustDataCallRector` — consistent with the project's one-class-per-file convention.

### Issue number mismatches
Three rectors reference a different `@see` issue number than the digest filename's suffix. In each case the transformation is the same; the numbers refer to different nodes in the same deprecation issue thread:

| Rector | Rector `@see` | Digest filename issue | Notes |
|---|---|---|---|
| `RemoveViewsRowCacheKeysRector` | `#3564958` | `#3564937` | `3564958` is the change record; `3564937` is the original issue |
| `ReplaceCommentManagerGetCountNewCommentsRector` | `#3551729` | `#3543035` | `3543035` is the original issue; `3551729` is the related change record |
| `ReplaceEntityReferenceRecursiveLimitRector` | `#3316878` | `#2940605` | `2940605` is the older issue; `3316878` is the more recent change record |
