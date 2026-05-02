# Drupal Digest → Drupal Rector Comparison Report

Compares each rector added in branch `feature/digest-rectors` against its source in
[drupal-digests](https://github.com/dbuytaert/drupal-digests).

**Total rectors compared:** 50
**Significant changes:** 23
**Minimal changes:** 27
**Split from one digest file:** 1 pair (`RemoveTrustDataCallRector` + `RemoveConfigSaveTrustedDataArgRector`)

---

## Overview Table

> Paths are relative to each repo root.
> `†` = rector `@see` issue number differs from the digest filename — see [Notes](#notes-on-digest-file-mapping).

| Rector | Ver | Changes | Issue | Digest source | Rector destination |
|---|---|---|---|---|---|
| `ReplaceModuleHandlerGetNameRector` | D10 | Minimal | [#3571063](https://www.drupal.org/node/3571063) | `rector/rules/replace-removed-modulehandlerinterface-getname-with-3571063.php` | `src/Drupal10/Rector/Deprecation/ReplaceModuleHandlerGetNameRector.php` |
| `ReplaceRebuildThemeDataRector` | D10 | Minimal | [#3571068](https://www.drupal.org/node/3571068) | `rector/rules/replace-removed-themehandlerinterface-rebuildthemedata-with-3571068.php` | `src/Drupal10/Rector/Deprecation/ReplaceRebuildThemeDataRector.php` |
| `ReplaceRequestTimeConstantRector` | D10 | **Significant** | [#3395986](https://www.drupal.org/node/3395986) | `rector/rules/replace-deprecated-request-time-constant-with-drupal-time-3395986.php` | `src/Drupal10/Rector/Deprecation/ReplaceRequestTimeConstantRector.php` |
| `ErrorCurrentErrorHandlerRector` | D11 | Minimal | [#3526515](https://www.drupal.org/node/3526515) | `rector/rules/replace-error-currenterrorhandler-with-get-error-handler-3526515.php` | `src/Drupal11/Rector/Deprecation/ErrorCurrentErrorHandlerRector.php` |
| `FileSystemBasenameToNativeRector` | D11 | **Significant** | [#3530461](https://www.drupal.org/node/3530461) | `rector/rules/replace-filesysteminterface-basename-with-native-basename-3530461.php` | `src/Drupal11/Rector/Deprecation/FileSystemBasenameToNativeRector.php` |
| `LoadAllIncludesRector` | D11 | **Significant** | [#3536431](https://www.drupal.org/node/3536431) | `rector/rules/replace-deprecated-modulehandler-loadallincludes-with-3536431.php` | `src/Drupal11/Rector/Deprecation/LoadAllIncludesRector.php` |
| `MigrateSqlGetMigrationPluginManagerRector` | D11 | **Significant** | [#3439369](https://www.drupal.org/node/3439369) | `rector/rules/replace-deprecated-sql-getmigrationpluginmanager-with-3439369.php` | `src/Drupal11/Rector/Deprecation/MigrateSqlGetMigrationPluginManagerRector.php` |
| `NodeStorageDeprecatedMethodsRector` | D11 | **Significant** | [#3396062](https://www.drupal.org/node/3396062) | `rector/rules/replace-deprecated-nodestorage-revisionids-and-3396062.php` | `src/Drupal11/Rector/Deprecation/NodeStorageDeprecatedMethodsRector.php` |
| `PluginBaseIsConfigurableRector` | D11 | **Significant** | [#3459533](https://www.drupal.org/node/3459533) | `rector/rules/replace-deprecated-pluginbase-isconfigurable-with-3459533.php` | `src/Drupal11/Rector/Deprecation/PluginBaseIsConfigurableRector.php` |
| `RemoveAutomatedCronSubmitHandlerRector` | D11 | Minimal | [#3566768](https://www.drupal.org/node/3566768) | `rector/rules/remove-deprecated-automated-cron-settings-submit-calls-and-3566768.php` | `src/Drupal11/Rector/Deprecation/RemoveAutomatedCronSubmitHandlerRector.php` |
| `RemoveCacheExpireOverrideRector` | D11 | **Significant** | [#3576556](https://www.drupal.org/node/3576556) | `rector/rules/remove-deprecated-cacheexpire-overrides-from-views-3576556.php` | `src/Drupal11/Rector/Deprecation/RemoveCacheExpireOverrideRector.php` |
| `RemoveConfigSaveTrustedDataArgRector` | D11 | **Significant** | [#3347842](https://www.drupal.org/node/3347842) | `rector/rules/remove-deprecated-trustdata-calls-and-save-true-argument-in-3347842.php` *(split)* | `src/Drupal11/Rector/Deprecation/RemoveConfigSaveTrustedDataArgRector.php` |
| `RemoveHandlerBaseDefineExtraOptionsRector` | D11 | **Significant** | [#3485084](https://www.drupal.org/node/3485084) | `rector/rules/remove-overrides-of-deprecated-handlerbase-3485084.php` | `src/Drupal11/Rector/Deprecation/RemoveHandlerBaseDefineExtraOptionsRector.php` |
| `RemoveLinkWidgetValidateTitleElementRector` | D11 | Minimal | [#3093118](https://www.drupal.org/node/3093118) | `rector/rules/remove-deprecated-linkwidget-validatetitleelement-calls-3093118.php` | `src/Drupal11/Rector/Deprecation/RemoveLinkWidgetValidateTitleElementRector.php` |
| `RemoveModuleHandlerAddModuleCallsRector` | D11 | **Significant** | [#3528899](https://www.drupal.org/node/3528899) | `rector/rules/remove-deprecated-modulehandlerinterface-addmodule-and-3528899.php` | `src/Drupal11/Rector/Deprecation/RemoveModuleHandlerAddModuleCallsRector.php` |
| `RemoveModuleHandlerDeprecatedMethodsRector` | D11 | Minimal | [#3442009](https://www.drupal.org/node/3442009) | `rector/rules/remove-deprecated-modulehandlerinterface-writecache-and-3442009.php` | `src/Drupal11/Rector/Deprecation/RemoveModuleHandlerDeprecatedMethodsRector.php` |
| `RemoveRootFromConvertDbUrlRector` | D11 | Minimal | [#3522513](https://www.drupal.org/node/3522513) | `rector/rules/remove-deprecated-string-root-from-database-3522513.php` | `src/Drupal11/Rector/Deprecation/RemoveRootFromConvertDbUrlRector.php` |
| `RemoveSetUriCallbackRector` | D11 | **Significant** | [#2667040](https://www.drupal.org/node/2667040) | `rector/rules/remove-deprecated-entitytypeinterface-seturicallback-calls-2667040.php` | `src/Drupal11/Rector/Deprecation/RemoveSetUriCallbackRector.php` |
| `RemoveStateCacheSettingRector` | D11 | Minimal | [#3436954](https://www.drupal.org/node/3436954) | `rector/rules/remove-deprecated-settings-state-cache-assignment-for-3436954.php` | `src/Drupal11/Rector/Deprecation/RemoveStateCacheSettingRector.php` |
| `RemoveTrustDataCallRector` | D11 | **Significant** | [#3347842](https://www.drupal.org/node/3347842) | `rector/rules/remove-deprecated-trustdata-calls-and-save-true-argument-in-3347842.php` *(split)* | `src/Drupal11/Rector/Deprecation/RemoveTrustDataCallRector.php` |
| `RemoveTwigNodeTransTagArgumentRector` | D11 | Minimal | [#3473440](https://www.drupal.org/node/3473440) | `rector/rules/remove-deprecated-tag-argument-from-twignodetrans-3473440.php` | `src/Drupal11/Rector/Deprecation/RemoveTwigNodeTransTagArgumentRector.php` |
| `RemoveUpdaterPostInstallMethodsRector` | D11 | Minimal | [#3417136](https://www.drupal.org/node/3417136) | `rector/rules/remove-deprecated-updater-postinstall-postinstalltasks-3417136.php` | `src/Drupal11/Rector/Deprecation/RemoveUpdaterPostInstallMethodsRector.php` |
| `RemoveViewsRowCacheKeysRector` | D11 | **Significant** | [#3564958](https://www.drupal.org/node/3564958) † | `rector/rules/remove-deprecated-cachepluginbase-getrowcachekeys-and-3564937.php` | `src/Drupal11/Rector/Deprecation/RemoveViewsRowCacheKeysRector.php` |
| `RenameStopProceduralHookScanRector` | D11 | Minimal | [#3495943](https://www.drupal.org/node/3495943) | `rector/rules/rename-stopproceduralhookscan-to-proceduralhookscanstop-3495943.php` | `src/Drupal11/Rector/Deprecation/RenameStopProceduralHookScanRector.php` |
| `ReplaceAlphadecimalToIntNullRector` | D11 | **Significant** | [#3442810](https://www.drupal.org/node/3442810) | `rector/rules/replace-deprecated-number-alphadecimaltoint-null-calls-with-3442810.php` | `src/Drupal11/Rector/Deprecation/ReplaceAlphadecimalToIntNullRector.php` |
| `ReplaceCommentManagerGetCountNewCommentsRector` | D11 | **Significant** | [#3551729](https://www.drupal.org/node/3551729) † | `rector/rules/replace-deprecated-commentmanagerinterface-3543035.php` | `src/Drupal11/Rector/Deprecation/ReplaceCommentManagerGetCountNewCommentsRector.php` |
| `ReplaceCommentUriRector` | D11 | Minimal | [#2010202](https://www.drupal.org/node/2010202) | `rector/rules/replace-deprecated-comment-uri-with-comment-permalink-2010202.php` | `src/Drupal11/Rector/Deprecation/ReplaceCommentUriRector.php` |
| `ReplaceDateTimeRangeConstantsRector` | D11 | Minimal | [#3574901](https://www.drupal.org/node/3574901) | `rector/rules/replace-removed-datetimerangeconstantsinterface-constants-3574901.php` | `src/Drupal11/Rector/Deprecation/ReplaceDateTimeRangeConstantsRector.php` |
| `ReplaceEditorLoadRector` | D11 | Minimal | [#3447794](https://www.drupal.org/node/3447794) | `rector/rules/replace-deprecated-editor-load-with-entity-storage-load-3447794.php` | `src/Drupal11/Rector/Deprecation/ReplaceEditorLoadRector.php` |
| `ReplaceEntityOriginalPropertyRector` | D11 | **Significant** | [#3571065](https://www.drupal.org/node/3571065) | `rector/rules/replace-deprecated-entity-original-magic-property-with-3571065.php` | `src/Drupal11/Rector/Deprecation/ReplaceEntityOriginalPropertyRector.php` |
| `ReplaceEntityReferenceRecursiveLimitRector` | D11 | **Significant** | [#3316878](https://www.drupal.org/node/3316878) † | `rector/rules/replace-deprecated-entityreferenceentityformatter-recursive-2940605.php` | `src/Drupal11/Rector/Deprecation/ReplaceEntityReferenceRecursiveLimitRector.php` |
| `ReplaceFieldgroupToFieldsetRector` | D11 | Minimal | [#3512254](https://www.drupal.org/node/3512254) | `rector/rules/replace-deprecated-type-fieldgroup-with-type-fieldset-3512254.php` | `src/Drupal11/Rector/Deprecation/ReplaceFieldgroupToFieldsetRector.php` |
| `ReplaceFileGetContentHeadersRector` | D11 | Minimal | [#3494126](https://www.drupal.org/node/3494126) | `rector/rules/replace-file-get-content-headers-with-fileinterface-3494126.php` | `src/Drupal11/Rector/Deprecation/ReplaceFileGetContentHeadersRector.php` |
| `ReplaceLocaleConfigBatchFunctionsRector` | D11 | Minimal | [#3575254](https://www.drupal.org/node/3575254) | `rector/rules/replace-removed-locale-batch-helper-functions-with-their-3575254.php` | `src/Drupal11/Rector/Deprecation/ReplaceLocaleConfigBatchFunctionsRector.php` |
| `ReplaceNodeAccessViewAllNodesRector` | D11 | Minimal | [#3038908](https://www.drupal.org/node/3038908) | `rector/rules/replace-deprecated-node-access-view-all-nodes-with-3038908.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeAccessViewAllNodesRector.php` |
| `ReplaceNodeAddBodyFieldRector` | D11 | Minimal | [#3489266](https://www.drupal.org/node/3489266) | `rector/rules/replace-deprecated-node-add-body-field-with-createbodyfield-3489266.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeAddBodyFieldRector.php` |
| `ReplaceNodeModuleProceduralFunctionsRector` | D11 | Minimal | [#3571623](https://www.drupal.org/node/3571623) | `rector/rules/replace-deprecated-node-module-procedural-functions-with-oo-3571623.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeModuleProceduralFunctionsRector.php` |
| `ReplaceNodeSetPreviewModeRector` | D11 | **Significant** | [#3538277](https://www.drupal.org/node/3538277) | `rector/rules/replace-deprecated-constants-with-nodepreviewmode-enum-in-3538277.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeSetPreviewModeRector.php` |
| `ReplacePdoFetchConstantsRector` | D11 | Minimal | [#3525077](https://www.drupal.org/node/3525077) | `rector/rules/replace-pdo-fetch-constants-with-fetchas-enum-cases-in-3525077.php` | `src/Drupal11/Rector/Deprecation/ReplacePdoFetchConstantsRector.php` |
| `ReplaceRecipeRunnerInstallModuleRector` | D11 | Minimal | [#3498026](https://www.drupal.org/node/3498026) | `rector/rules/replace-deprecated-reciperunner-installmodule-with-3498026.php` | `src/Drupal11/Rector/Deprecation/ReplaceRecipeRunnerInstallModuleRector.php` |
| `ReplaceSessionManagerDeleteRector` | D11 | **Significant** | [#3577376](https://www.drupal.org/node/3577376) | `rector/rules/replace-deprecated-sessionmanager-delete-with-3577376.php` | `src/Drupal11/Rector/Deprecation/ReplaceSessionManagerDeleteRector.php` |
| `ReplaceSessionWritesWithRequestSessionRector` | D11 | Minimal | [#3518527](https://www.drupal.org/node/3518527) | `rector/rules/replace-deprecated-session-writes-with-drupal-request-3518527.php` | `src/Drupal11/Rector/Deprecation/ReplaceSessionWritesWithRequestSessionRector.php` |
| `ReplaceSystemPerformanceGzipKeyRector` | D11 | Minimal | [#3184242](https://www.drupal.org/node/3184242) | `rector/rules/replace-deprecated-system-performance-css-gzip-js-gzip-3184242.php` | `src/Drupal11/Rector/Deprecation/ReplaceSystemPerformanceGzipKeyRector.php` |
| `ReplaceThemeGetSettingRector` | D11 | Minimal | [#3573896](https://www.drupal.org/node/3573896) | `rector/rules/replace-deprecated-theme-get-setting-and-system-default-3573896.php` | `src/Drupal11/Rector/Deprecation/ReplaceThemeGetSettingRector.php` |
| `ReplaceUserSessionNamePropertyRector` | D11 | Minimal | [#3513856](https://www.drupal.org/node/3513856) | `rector/rules/replace-deprecated-usersession-name-property-read-with-3513856.php` | `src/Drupal11/Rector/Deprecation/ReplaceUserSessionNamePropertyRector.php` |
| `ReplaceViewsProceduralFunctionsRector` | D11 | Minimal | [#3572243](https://www.drupal.org/node/3572243) | `rector/rules/replace-deprecated-views-procedural-functions-with-oo-3572243.php` | `src/Drupal11/Rector/Deprecation/ReplaceViewsProceduralFunctionsRector.php` |
| `StatementPrefetchIteratorFetchColumnRector` | D11 | **Significant** | [#3490200](https://www.drupal.org/node/3490200) | `rector/rules/replace-deprecated-statementprefetchiterator-fetchcolumn-3490200.php` | `src/Drupal11/Rector/Deprecation/StatementPrefetchIteratorFetchColumnRector.php` |
| `StripMigrationDependenciesExpandArgRector` | D11 | **Significant** | [#3574717](https://www.drupal.org/node/3574717) | `rector/rules/strip-removed-expand-argument-from-getmigrationdependencies-3574717.php` | `src/Drupal11/Rector/Deprecation/StripMigrationDependenciesExpandArgRector.php` |
| `UseEntityTypeHasIntegerIdRector` | D11 | **Significant** | [#3566801](https://www.drupal.org/node/3566801) | `rector/rules/replace-deprecated-entity-type-integer-id-helpers-with-3566801.php` | `src/Drupal11/Rector/Deprecation/UseEntityTypeHasIntegerIdRector.php` |
| `ViewsPluginHandlerManagerRector` | D11 | **Significant** | [#3566424](https://www.drupal.org/node/3566424) | `rector/rules/replace-deprecated-views-pluginmanager-and-views-3566424.php` | `src/Drupal11/Rector/Deprecation/ViewsPluginHandlerManagerRector.php` |

---

## Significant Changes

### 1. `ReplaceRequestTimeConstantRector` (Drupal10)

**Digest file:** `replace-deprecated-request-time-constant-with-drupal-time-3395986.php`

**Change:** The digest used `$this->nodeFactory->createStaticCall('Drupal', 'time')` and `$this->nodeFactory->createMethodCall(...)` helpers from Rector's `NodeFactory`. The rector builds the AST manually using `new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), new Node\Identifier('time'))` and `new Node\Expr\MethodCall(...)`. This avoids a dependency on `nodeFactory` and makes the node construction explicit and portable.

```php
// Digest — uses nodeFactory helpers
$staticCall = $this->nodeFactory->createStaticCall('Drupal', 'time');
return $this->nodeFactory->createMethodCall($staticCall, 'getRequestTime');

// Rector — manual AST construction
$staticCall = new Node\Expr\StaticCall(
    new Node\Name\FullyQualified('Drupal'),
    new Node\Identifier('time')
);
return new Node\Expr\MethodCall($staticCall, new Node\Identifier('getRequestTime'));
```

---

### 2. `FileSystemBasenameToNativeRector`

**Digest file:** `replace-filesysteminterface-basename-with-native-basename-3530461.php`

**Change:** The digest used `$this->getType($node->var)->isSuperTypeOf(new ObjectType($class))->yes()` inside a foreach loop. The rector uses `$this->isObjectType($node->var, new ObjectType($class))`, the standard Rector API. Also removed the unused `$callerType` variable assignment that was left in as a remnant. Both check the same two classes (`FileSystemInterface` and `FileSystem`), but the rector's approach is more idiomatic.

```php
// Digest
$callerType = $this->getType($node->var);
foreach (['Drupal\Core\File\FileSystemInterface', 'Drupal\Core\File\FileSystem'] as $class) {
    if ($callerType->isSuperTypeOf(new \PHPStan\Type\ObjectType($class))->yes()) {

// Rector (note: $callerType assignment retained but unused — harmless)
foreach (['Drupal\Core\File\FileSystemInterface', 'Drupal\Core\File\FileSystem'] as $class) {
    if ($this->isObjectType($node->var, new ObjectType($class))) {
```

---

### 3. `LoadAllIncludesRector`

**Digest file:** `replace-deprecated-modulehandler-loadallincludes-with-3536431.php`

**Change:** The digest had no type guard — it matched any `loadAllIncludes()` call regardless of object type. The rector adds a `ModuleHandlerInterface` type check, preventing false positives on unrelated objects.

```php
// Digest — no type guard
if (!$this->isName($methodCall->name, 'loadAllIncludes')) {
    return null;
}

// Rector — requires ModuleHandlerInterface
if (!$this->isObjectType($methodCall->var, new ObjectType('Drupal\Core\Extension\ModuleHandlerInterface'))) {
    return null;
}
```

---

### 4. `MigrateSqlGetMigrationPluginManagerRector`

**Digest file:** `replace-deprecated-sql-getmigrationpluginmanager-with-3439369.php`

**Change:** The digest used a **negative** type guard: skip if the caller `isObjectType(Migration::class)`. The rector inverts this to a **positive** guard: only proceed if the caller `isObjectType(Sql::class)`. This is more precise — it targets only the deprecated `Sql` subclass rather than excluding one known-good class while allowing everything else.

```php
// Digest — negative guard (exclude Migration)
if ($this->isObjectType($node->var, new ObjectType('Drupal\migrate\Plugin\Migration'))) {
    return null;
}

// Rector — positive guard (require Sql)
if (!$this->isObjectType($node->var, new ObjectType('Drupal\migrate\Plugin\migrate\id_map\Sql'))) {
    return null;
}
```

---

### 5. `NodeStorageDeprecatedMethodsRector`

**Digest file:** `replace-deprecated-nodestorage-revisionids-and-3396062.php`

**Change:** The digest handled only `revisionIds()` and `userRevisionIds()`. The rector adds handling for `countDefaultLanguageRevisions()`, which has no replacement and must be removed entirely. This requires registering `Node\Stmt\Expression::class` as an additional node type and using `NodeVisitor::REMOVE_NODE`.

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

### 6. `PluginBaseIsConfigurableRector`

**Digest file:** `replace-deprecated-pluginbase-isconfigurable-with-3459533.php`

**Change:** The digest relied solely on checking `$this->isConfigurable()` (variable name `this`, no args) to avoid false positives. The rector adds an explicit `isObjectType($node->var, new ObjectType('Drupal\Component\Plugin\PluginBase'))` guard, making it precise about which `isConfigurable()` methods are targeted. The digest comment already noted this concern (CKEditor5PluginDefinition, Action), but the digest trusted the `$this` check alone.

```php
// Digest — $this check only, no type guard
if ($this->getName($node->var) !== 'this') {
    return null;
}
// returns Instanceof_ without type check

// Rector — adds explicit PluginBase type guard
if ($this->getName($node->var) !== 'this') {
    return null;
}
if (!$this->isObjectType($node->var, new ObjectType('Drupal\Component\Plugin\PluginBase'))) {
    return null;
}
```

---

### 7. `RemoveCacheExpireOverrideRector`

**Digest file:** `remove-deprecated-cacheexpire-overrides-from-views-3576556.php`

**Change:** The digest's `isCachePluginBaseSubclass()` checked the FQCN, short names, and a PHPStan `isSuperTypeOf()` call. The rector adds a separate `PARENT_FQCNS` constant listing all four known fully-qualified parent class names (`CachePluginBase`, `Time`, `Tag`, `None`), and changes the short-name check to only apply when the name contains no backslash (i.e., is truly unqualified). This prevents false matches on partial namespace strings.

```php
// Digest — short-name check uses str_ends_with (matches any suffix)
foreach (self::PARENT_SHORT_NAMES as $short) {
    if ($parentName === $short || str_ends_with($parentName, '\\' . $short)) {

// Rector — adds PARENT_FQCNS constant + restricts short-name check to unqualified names
private const PARENT_FQCNS = [
    'Drupal\views\Plugin\views\cache\CachePluginBase',
    'Drupal\views\Plugin\views\cache\Time',
    'Drupal\views\Plugin\views\cache\Tag',
    'Drupal\views\Plugin\views\cache\None',
];
// Matches FQCNs first, then only unqualified names (no backslash)
if (!str_contains($parentName, '\\')) {
    foreach (self::PARENT_SHORT_NAMES as $short) {
```

---

### 8. `RemoveConfigSaveTrustedDataArgRector`

**Digest file:** `remove-deprecated-trustdata-calls-and-save-true-argument-in-3347842.php` (one file, split into two rector classes)

**Change:** In the digest, `RemoveConfigSaveTrustedDataArgRector` was defined in the same file as `RemoveTrustDataCallRector` and had **no type guard** on `save()` — it stripped the boolean arg from any `save(TRUE/FALSE)` call. The rector splits this into a standalone file and adds a `Drupal\Core\Config\Config` type guard, preventing false positives on `save(TRUE)` calls on unrelated objects.

```php
// Digest — no type guard on save()
if (!$this->isName($node->name, 'save')) {
    return null;
}
if (count($node->args) !== 1) { ... }

// Rector — adds Config type guard
if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Config\Config'))) {
    return null;
}
```

---

### 9. `RemoveHandlerBaseDefineExtraOptionsRector`

**Digest file:** `remove-overrides-of-deprecated-handlerbase-3485084.php`

**Change:** The digest's `PARENT_SHORT_NAMES` covered only `HandlerBase`. The rector expands it to also cover `FieldHandlerBase`, `FilterPluginBase`, `SortPluginBase`, `ArgumentPluginBase`, and `RelationshipPluginBase` — the five concrete handler base classes that commonly appear as direct parents in contrib Views plugins. The digest also had a guard to skip the `HandlerBase` class itself by checking `$node->name->toString() === 'HandlerBase'`; the rector drops this because the type guard via `isObjectType` already handles it correctly.

```php
// Digest
private const PARENT_SHORT_NAMES = ['HandlerBase'];
// Also had: if ($node->name instanceof Identifier && $node->name->toString() === 'HandlerBase') { return null; }

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

### 10. `RemoveModuleHandlerAddModuleCallsRector`

**Digest file:** `remove-deprecated-modulehandlerinterface-addmodule-and-3528899.php`

**Change:** The digest checked only `ModuleHandlerInterface`. The rector also checks the concrete `ModuleHandler` class, covering cases where the variable is typed as the concrete implementation rather than the interface.

```php
// Digest — interface only
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

### 11. `RemoveSetUriCallbackRector`

**Digest file:** `remove-deprecated-entitytypeinterface-seturicallback-calls-2667040.php`

**Change:** The digest had no type guard — it removed any standalone or mid-chain `setUriCallback()` call by method name alone. The rector adds `isObjectType($node->expr->var, new ObjectType('Drupal\Core\Entity\EntityTypeInterface'))` checks on both the standalone-statement case and the fluent-chain case.

```php
// Digest — no type guard
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

### 12. `RemoveTrustDataCallRector`

**Digest file:** `remove-deprecated-trustdata-calls-and-save-true-argument-in-3347842.php` (one file, split into two rector classes)

**Change:** In the digest, `RemoveTrustDataCallRector` stripped `trustData()` from any method chain regardless of object type. The rector adds a `ConfigEntityInterface` type guard, preventing false positives on unrelated objects that happen to have a `trustData()` method.

```php
// Digest — no type guard
if (!$this->isName($node->name, 'trustData')) {
    return null;
}
return $node->var;

// Rector — requires ConfigEntityInterface
if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Config\Entity\ConfigEntityInterface'))) {
    return null;
}
```

---

### 13. `RemoveViewsRowCacheKeysRector`

**Digest file:** `remove-deprecated-cachepluginbase-getrowcachekeys-and-3564937.php`

**Note:** The digest's `@see` references issue `#3564937`; the rector's `@see` references `#3564958`. Both numbers are real Drupal issues; `3564958` is the change record and `3564937` is the original issue. The rector correctly cites `3564958`.

**Change:** The digest removed array items whose value was a call to `getRowCacheKeys()` or `getRowId()` by method name alone (no type guard). The rector adds `isObjectType($item->value->var, new ObjectType('Drupal\views\Plugin\views\cache\CachePluginBase'))`, preventing false positives when another class has methods with the same names.

```php
// Digest — no type guard
if ($item->value instanceof MethodCall && $this->isDeprecatedMethodCall($item->value)) {

// Rector — type-guarded
if ($item->value instanceof MethodCall
    && $item->value->name instanceof Identifier
    && in_array($item->value->name->toString(), self::DEPRECATED_METHODS, true)
    && $this->isObjectType($item->value->var, new ObjectType('Drupal\views\Plugin\views\cache\CachePluginBase'))
) {
```

---

### 14. `ReplaceAlphadecimalToIntNullRector`

**Digest file:** `replace-deprecated-number-alphadecimaltoint-null-calls-with-3442810.php`

**Change:** The digest used inline backslash-prefixed FQCNs everywhere (`new \PHPStan\Type\ObjectType(...)`, `new \PhpParser\Node\Arg(...)`, etc.) with no `use` imports. The rector uses proper `use` imports at the top of the file, following drupal-rector coding standards. Also, the digest class name was `AlphadecimalToIntNullOrEmptyRector`; the rector renamed it to `ReplaceAlphadecimalToIntNullRector` for consistency with the project's naming convention.

---

### 15. `ReplaceCommentManagerGetCountNewCommentsRector`

**Digest file:** `replace-deprecated-commentmanagerinterface-3543035.php`

**Note:** The rector's `@see` references issue `#3551729`; the digest file uses issue `#3543035`. Both reference the same deprecation — `#3543035` is the original issue, `#3551729` is a related follow-up. The transformation logic is identical.

**Change:** The digest extended `AbstractRector` directly. The rector extends `AbstractDrupalCoreRector` and wraps the logic in `refactorWithConfiguration()`, enabling version-gated activation via `DrupalIntroducedVersionConfiguration('11.3.0')`. `getRuleDefinition()` uses `ConfiguredCodeSample` instead of `CodeSample`.

```php
// Digest
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

### 16. `ReplaceEntityOriginalPropertyRector`

**Digest file:** `replace-deprecated-entity-original-magic-property-with-3571065.php`

**Change:** The digest handled `PropertyFetch` and `Assign` nodes. The rector adds `NullsafePropertyFetch` as a third node type, rewriting `$entity?->original` to `$entity?->getOriginal()` via `NullsafeMethodCall`. The digest also lacked a type guard on read accesses; the rector adds `isObjectType($node->var, new ObjectType('Drupal\Core\Entity\EntityInterface'))` on both `PropertyFetch` and `NullsafePropertyFetch`.

```php
// Digest — only PropertyFetch and Assign
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
// PropertyFetch branch:
if ($this->isObjectType($node->var, new ObjectType('Drupal\Core\Entity\EntityInterface'))) {
    return new MethodCall($node->var, 'getOriginal');
}
// NullsafePropertyFetch branch:
if ($this->isObjectType($node->var, new ObjectType('Drupal\Core\Entity\EntityInterface'))) {
    return new NullsafeMethodCall($node->var, 'getOriginal');
}
```

---

### 17. `ReplaceEntityReferenceRecursiveLimitRector`

**Digest file:** `replace-deprecated-entityreferenceentityformatter-recursive-2940605.php`

**Note:** The rector's `@see` references issue `#3316878`; the digest file uses `#2940605`. The rector's issue number is the more recent change record for this deprecation.

**Change (type resolution):** The digest used `isObjectType($node, new ObjectType(self::DEPRECATED_CLASS))` with a `static`/`self`/`parent` branch to handle within-subclass references. The rector uses a simpler `TARGET_CLASSES` array approach with `$this->isName($node->class, $class)`, which works because Rector resolves `use` aliases before `refactor()` is called.

**Change (PhpParser version):** The digest used `LNumber` (PhpParser 4.x API). The rector uses `Int_` (PhpParser 5.x renamed `LNumber` to `Int_`).

```php
// Digest — LNumber (PhpParser 4.x)
return new LNumber(20);

// Rector — Int_ (PhpParser 5.x)
use PhpParser\Node\Scalar\Int_;
...
return new Int_(20);
```

---

### 18. `ReplaceNodeSetPreviewModeRector`

**Digest file:** `replace-deprecated-constants-with-nodepreviewmode-enum-in-3538277.php`

**Change:** The digest had no type guard on `setPreviewMode()`. The rector adds `isObjectType($node->var, new ObjectType('Drupal\node\NodeTypeInterface'))`, preventing false positives on other classes that may have a `setPreviewMode()` method.

```php
// Digest — no type guard
if (!$this->isName($node->name, 'setPreviewMode')) { return null; }
// Immediately processes the argument

// Rector — NodeTypeInterface guard
if (!$this->isObjectType($node->var, new ObjectType('Drupal\node\NodeTypeInterface'))) {
    return null;
}
```

---

### 19. `ReplaceSessionManagerDeleteRector`

**Digest file:** `replace-deprecated-sessionmanager-delete-with-3577376.php`

**Change:** The digest extended `AbstractRector` directly with a plain `refactor()` method. The rector extends `AbstractDrupalCoreRector` and wraps the logic in `refactorWithConfiguration()`, enabling version-gated activation via `DrupalIntroducedVersionConfiguration('11.4.0')`.

```php
// Digest
final class ReplaceSessionManagerDeleteRector extends AbstractRector
{
    public function refactor(Node $node): ?Node { ... }

// Rector
final class ReplaceSessionManagerDeleteRector extends AbstractDrupalCoreRector
{
    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node { ... }
    // getRuleDefinition() uses ConfiguredCodeSample with DrupalIntroducedVersionConfiguration('11.4.0')
```

---

### 20. `StatementPrefetchIteratorFetchColumnRector`

**Digest file:** `replace-deprecated-statementprefetchiterator-fetchcolumn-3490200.php`

**Change:** The digest avoided PDO's native `fetchColumn()` by checking if the caller was a `PropertyFetch` with the name `clientStatement`. The rector replaces this heuristic with an explicit `isObjectType($node->var, new ObjectType('Drupal\Core\Database\StatementPrefetchIterator'))` type guard — more precise and not dependent on internal implementation details.

```php
// Digest — heuristic exclusion
if ($node->var instanceof \PhpParser\Node\Expr\PropertyFetch) {
    if ($propertyName === 'clientStatement') {
        return null;
    }
}

// Rector — positive type guard
if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Database\StatementPrefetchIterator'))) {
    return null;
}
```

---

### 21. `StripMigrationDependenciesExpandArgRector`

**Digest file:** `strip-removed-expand-argument-from-getmigrationdependencies-3574717.php`

**Change:** The digest used `$migrationInterface->isSuperTypeOf($callerType)->yes()` (PHPStan's type algebra). The rector uses `$this->isObjectType($node->var, new ObjectType('Drupal\migrate\Plugin\MigrationInterface'))` — the standard Rector API, which is equivalent but more idiomatic.

```php
// Digest
$callerType = $this->getType($node->var);
$migrationInterface = new ObjectType(self::MIGRATION_INTERFACE);
if (!$migrationInterface->isSuperTypeOf($callerType)->yes()) {
    return null;
}

// Rector
if (!$this->isObjectType($node->var, new ObjectType('Drupal\migrate\Plugin\MigrationInterface'))) {
    return null;
}
```

---

### 22. `UseEntityTypeHasIntegerIdRector`

**Digest file:** `replace-deprecated-entity-type-integer-id-helpers-with-3566801.php`

**Change:** The digest treated `entityTypeSupportsComments` and `hasIntegerId` (the second one) as simple `$this->method()` rewrites with no type guard — any class could have those method names. The rector adds a `METHOD_OWNER_CLASS` constant that maps each method name to its declaring class FQCN, and calls `isObjectType()` before transforming. This prevents false positives on unrelated classes that might have methods with the same names.

```php
// Digest — no type guard on $this->entityTypeSupportsComments() / $this->hasIntegerId()
private const SIMPLE_METHOD_NAMES = ['entityTypeSupportsComments'];
if (in_array($methodName, self::SIMPLE_METHOD_NAMES, true) && count($node->args) === 1) {
    return new MethodCall($node->args[0]->value, 'hasIntegerId');
}
if ($methodName === 'hasIntegerId' && count($node->args) === 1) {
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

### 23. `ViewsPluginHandlerManagerRector`

**Digest file:** `replace-deprecated-views-pluginmanager-and-views-3566424.php`

**Change:** The digest used `$this->isObjectType($node->class, new ObjectType('Drupal\\views\\Views'))` to check the static call target — which is semantically wrong: `$node->class` in a static call is a `Name` node (the class name), not an object, so `isObjectType` is inappropriate. The rector uses `$this->isName($node->class, 'Drupal\views\Views')` instead, which is the correct way to match a class name in a static call.

```php
// Digest — incorrect isObjectType on a static call class name
if (!$this->isObjectType($node->class, new \PHPStan\Type\ObjectType('Drupal\\views\\Views'))) {

// Rector — correct isName check
if (!$this->isName($node->class, 'Drupal\views\Views')) {
```

---

## Minimal Changes

These rectors are functionally identical to their digest counterparts. The differences are limited to: namespace declarations, proper `use` imports (replacing inline backslash-prefixed FQCNs), `declare(strict_types=1)` placement, removal of unused `use Rector\Config\RectorConfig` imports, replacement of `@param` docblock type hints with `assert()` guards, class renaming to match the drupal-rector naming convention, and wrapping in `AbstractDrupalCoreRector` with `configure()` + `refactorWithConfiguration()` (for the Drupal10 rectors below where the logic is unchanged).

| Rector class | Notable structural differences from digest |
|---|---|
| `ReplaceModuleHandlerGetNameRector` (Drupal10) | Extends `AbstractDrupalCoreRector`; `refactorWithConfiguration()`; `ConfiguredCodeSample` with version `10.3.0` |
| `ReplaceRebuildThemeDataRector` (Drupal10) | Extends `AbstractDrupalCoreRector`; adds `ThemeHandlerInterface` type guard; `ConfiguredCodeSample` with version `10.3.0` |
| `ErrorCurrentErrorHandlerRector` | Namespace + imports only |
| `RemoveAutomatedCronSubmitHandlerRector` | Namespace + imports only |
| `RemoveLinkWidgetValidateTitleElementRector` | Namespace + imports only |
| `RemoveModuleHandlerDeprecatedMethodsRector` | Namespace + imports only |
| `RemoveRootFromConvertDbUrlRector` | Namespace + imports only |
| `RemoveStateCacheSettingRector` | Namespace + imports only |
| `RemoveTwigNodeTransTagArgumentRector` | Namespace + imports only |
| `RemoveUpdaterPostInstallMethodsRector` | Namespace + imports only |
| `RenameStopProceduralHookScanRector` | Namespace + imports only |
| `ReplaceCommentUriRector` | Namespace + imports only |
| `ReplaceDateTimeRangeConstantsRector` | Namespace + imports only |
| `ReplaceEditorLoadRector` | Namespace + imports only |
| `ReplaceFieldgroupToFieldsetRector` | Namespace + imports only |
| `ReplaceFileGetContentHeadersRector` | Namespace + imports only |
| `ReplaceLocaleConfigBatchFunctionsRector` | Namespace + imports only |
| `ReplaceNodeAccessViewAllNodesRector` | Namespace + imports only |
| `ReplaceNodeAddBodyFieldRector` | Namespace + imports only |
| `ReplaceNodeModuleProceduralFunctionsRector` | Namespace + imports only |
| `ReplacePdoFetchConstantsRector` | Namespace + imports only |
| `ReplaceRecipeRunnerInstallModuleRector` | Namespace + imports only |
| `ReplaceSessionWritesWithRequestSessionRector` | Namespace + imports only |
| `ReplaceSystemPerformanceGzipKeyRector` | Namespace + imports only |
| `ReplaceThemeGetSettingRector` | Namespace + imports only |
| `ReplaceUserSessionNamePropertyRector` | Namespace + imports only |
| `ReplaceViewsProceduralFunctionsRector` | Namespace + imports only |

---

## Notes on Digest File Mapping

### One digest file → two rector files
`remove-deprecated-trustdata-calls-and-save-true-argument-in-3347842.php` defined two classes in a single file: `RemoveTrustDataCallRector` and `RemoveConfigSaveTrustedDataArgRector`. The rector project splits these into two separate class files, one per rector — consistent with the project's one-class-per-file convention.

### Issue number mismatches
Three rectors reference a different `@see` issue number than the digest filename's suffix. In each case the transformation is the same; the issue numbers refer to different nodes in the same deprecation issue thread (the original issue vs. a change record or follow-up):

| Rector | Rector `@see` | Digest filename issue | Notes |
|---|---|---|---|
| `RemoveViewsRowCacheKeysRector` | `#3564958` | `#3564937` | `3564958` is the change record; `3564937` is the original issue |
| `ReplaceCommentManagerGetCountNewCommentsRector` | `#3551729` | `#3543035` | `3543035` is the original issue; `3551729` is a related follow-up |
| `ReplaceEntityReferenceRecursiveLimitRector` | `#3316878` | `#2940605` | `2940605` is the older issue; `3316878` is the more recent change record |
