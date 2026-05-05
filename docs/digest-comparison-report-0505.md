# Drupal Digest → Drupal Rector Comparison Report (drupal-digest-0503)

Compares each rector added in branch `feature/digest-rectors` against its source in
[drupal-digest-0503](https://github.com/dbuytaert/drupal-digests) (refreshed May 2025).

**Total rectors compared:** 50
**Significant changes:** 21
**Minimal changes:** 28
**Split from one digest file:** 1 pair (`RemoveTrustDataCallRector` + `RemoveConfigSaveTrustedDataArgRector`)

---

## Overview Table

> Paths are relative to each repo root. Digest paths are relative to `drupal-digest-0503/`.
> `†` = rector `@see` issue number differs from the digest filename — see [Notes](#notes-on-digest-file-mapping).

| Rector | Ver | Changes | Issue | Digest source | Rector destination |
|---|---|---|---|---|---|
| `ReplaceModuleHandlerGetNameRector` | D10 | Minimal | [#3571063](https://www.drupal.org/node/3571063) | `rector/rules/replace-removed-modulehandlerinterface-getname-with-3571063.php` | `src/Drupal10/Rector/Deprecation/ReplaceModuleHandlerGetNameRector.php` |
| `ReplaceRebuildThemeDataRector` | D10 | Minimal | [#3571068](https://www.drupal.org/node/3571068) | `rector/rules/replace-removed-themehandlerinterface-rebuildthemedata-with-3571068.php` | `src/Drupal10/Rector/Deprecation/ReplaceRebuildThemeDataRector.php` |
| `ReplaceRequestTimeConstantRector` | D10 | **Significant** | [#3395986](https://www.drupal.org/node/3395986) | `rector/rules/add-timeinterface-time-argument-to-plugin-constructor-3395986.php` | `src/Drupal10/Rector/Deprecation/ReplaceRequestTimeConstantRector.php` |
| `ErrorCurrentErrorHandlerRector` | D11 | Minimal | [#3526515](https://www.drupal.org/node/3526515) | `rector/rules/replace-error-currenterrorhandler-with-get-error-handler-3526515.php` | `src/Drupal11/Rector/Deprecation/ErrorCurrentErrorHandlerRector.php` |
| `FileSystemBasenameToNativeRector` | D11 | Minimal | [#3530461](https://www.drupal.org/node/3530461) | `rector/rules/replace-filesysteminterface-basename-with-native-basename-3530461.php` | `src/Drupal11/Rector/Deprecation/FileSystemBasenameToNativeRector.php` |
| `LoadAllIncludesRector` | D11 | Minimal | [#3536431](https://www.drupal.org/node/3536431) | `rector/rules/replace-deprecated-modulehandler-loadallincludes-with-3536431.php` | `src/Drupal11/Rector/Deprecation/LoadAllIncludesRector.php` |
| `MigrateSqlGetMigrationPluginManagerRector` | D11 | Minimal | [#3439369](https://www.drupal.org/node/3439369) | `rector/rules/replace-deprecated-sql-getmigrationpluginmanager-with-3439369.php` | `src/Drupal11/Rector/Deprecation/MigrateSqlGetMigrationPluginManagerRector.php` |
| `NodeStorageDeprecatedMethodsRector` | D11 | **Significant** | [#3396062](https://www.drupal.org/node/3396062) | `rector/rules/replace-deprecated-nodestorage-revisionids-and-3396062.php` | `src/Drupal11/Rector/Deprecation/NodeStorageDeprecatedMethodsRector.php` |
| `PluginBaseIsConfigurableRector` | D11 | Minimal | [#3459533](https://www.drupal.org/node/3459533) | `rector/rules/replace-pluginbase-isconfigurable-with-instanceof-3459533.php` | `src/Drupal11/Rector/Deprecation/PluginBaseIsConfigurableRector.php` |
| `RemoveAutomatedCronSubmitHandlerRector` | D11 | **Significant** | [#3566768](https://www.drupal.org/node/3566768) | `rector/rules/remove-deprecated-automated-cron-settings-submit-calls-and-3566768.php` | `src/Drupal11/Rector/Deprecation/RemoveAutomatedCronSubmitHandlerRector.php` |
| `RemoveCacheExpireOverrideRector` | D11 | **Significant** | [#3576556](https://www.drupal.org/node/3576556) | `rector/rules/remove-deprecated-cacheexpire-overrides-from-views-3576556.php` | `src/Drupal11/Rector/Deprecation/RemoveCacheExpireOverrideRector.php` |
| `RemoveConfigSaveTrustedDataArgRector` | D11 | **Significant** | [#3347842](https://www.drupal.org/node/3347842) | `rector/rules/remove-deprecated-trusted-data-concept-from-drupal-config-3347842.php` *(split)* | `src/Drupal11/Rector/Deprecation/RemoveConfigSaveTrustedDataArgRector.php` |
| `RemoveHandlerBaseDefineExtraOptionsRector` | D11 | **Significant** | [#3485084](https://www.drupal.org/node/3485084) | `rector/rules/remove-overrides-of-deprecated-handlerbase-3485084.php` | `src/Drupal11/Rector/Deprecation/RemoveHandlerBaseDefineExtraOptionsRector.php` |
| `RemoveLinkWidgetValidateTitleElementRector` | D11 | Minimal | [#3093118](https://www.drupal.org/node/3093118) | `rector/rules/remove-deprecated-linkwidget-validatetitleelement-calls-3093118.php` | `src/Drupal11/Rector/Deprecation/RemoveLinkWidgetValidateTitleElementRector.php` |
| `RemoveModuleHandlerAddModuleCallsRector` | D11 | **Significant** | [#3528899](https://www.drupal.org/node/3528899) | `rector/rules/remove-deprecated-modulehandlerinterface-addmodule-and-3528899.php` | `src/Drupal11/Rector/Deprecation/RemoveModuleHandlerAddModuleCallsRector.php` |
| `RemoveModuleHandlerDeprecatedMethodsRector` | D11 | **Significant** | [#3442009](https://www.drupal.org/node/3442009) | `rector/rules/remove-deprecated-modulehandlerinterface-writecache-and-3442009.php` | `src/Drupal11/Rector/Deprecation/RemoveModuleHandlerDeprecatedMethodsRector.php` |
| `RemoveRootFromConvertDbUrlRector` | D11 | **Significant** | [#3522513](https://www.drupal.org/node/3522513) | `rector/rules/remove-deprecated-string-root-from-database-3522513.php` | `src/Drupal11/Rector/Deprecation/RemoveRootFromConvertDbUrlRector.php` |
| `RemoveSetUriCallbackRector` | D11 | Minimal | [#2667040](https://www.drupal.org/node/2667040) | `rector/rules/remove-deprecated-entitytypeinterface-seturicallback-calls-2667040.php` | `src/Drupal11/Rector/Deprecation/RemoveSetUriCallbackRector.php` |
| `RemoveStateCacheSettingRector` | D11 | Minimal | [#3436954](https://www.drupal.org/node/3436954) | `rector/rules/remove-deprecated-settings-state-cache-assignment-3436954.php` | `src/Drupal11/Rector/Deprecation/RemoveStateCacheSettingRector.php` |
| `RemoveTrustDataCallRector` | D11 | **Significant** | [#3347842](https://www.drupal.org/node/3347842) | `rector/rules/remove-deprecated-trusted-data-concept-from-drupal-config-3347842.php` *(split)* | `src/Drupal11/Rector/Deprecation/RemoveTrustDataCallRector.php` |
| `RemoveTwigNodeTransTagArgumentRector` | D11 | **Significant** | [#3473440](https://www.drupal.org/node/3473440) | `rector/rules/remove-deprecated-tag-argument-from-twignodetrans-3473440.php` | `src/Drupal11/Rector/Deprecation/RemoveTwigNodeTransTagArgumentRector.php` |
| `RemoveUpdaterPostInstallMethodsRector` | D11 | Minimal | [#3417136](https://www.drupal.org/node/3417136) | `rector/rules/remove-deprecated-updater-postinstall-postinstalltasks-3417136.php` | `src/Drupal11/Rector/Deprecation/RemoveUpdaterPostInstallMethodsRector.php` |
| `RemoveViewsRowCacheKeysRector` | D11 | Minimal | [#3564958](https://www.drupal.org/node/3564958) † | `rector/rules/remove-deprecated-cachepluginbase-getrowcachekeys-and-3564937.php` | `src/Drupal11/Rector/Deprecation/RemoveViewsRowCacheKeysRector.php` |
| `RenameStopProceduralHookScanRector` | D11 | **Significant** | [#3495943](https://www.drupal.org/node/3495943) | `rector/rules/rename-stopproceduralhookscan-attribute-to-3495943.php` | `src/Drupal11/Rector/Deprecation/RenameStopProceduralHookScanRector.php` |
| `ReplaceAlphadecimalToIntNullRector` | D11 | Minimal | [#3442810](https://www.drupal.org/node/3442810) | `rector/rules/replace-deprecated-number-alphadecimaltoint-null-calls-with-3442810.php` | `src/Drupal11/Rector/Deprecation/ReplaceAlphadecimalToIntNullRector.php` |
| `ReplaceCommentManagerGetCountNewCommentsRector` | D11 | **Significant** | [#3551729](https://www.drupal.org/node/3551729) † | `rector/rules/replace-deprecated-commentmanagerinterface-3543035.php` | `src/Drupal11/Rector/Deprecation/ReplaceCommentManagerGetCountNewCommentsRector.php` |
| `ReplaceCommentUriRector` | D11 | Minimal | [#2010202](https://www.drupal.org/node/2010202) | `rector/rules/replace-deprecated-comment-uri-with-comment-permalink-2010202.php` | `src/Drupal11/Rector/Deprecation/ReplaceCommentUriRector.php` |
| `ReplaceDateTimeRangeConstantsRector` | D11 | Minimal | [#3574901](https://www.drupal.org/node/3574901) | `rector/rules/replace-removed-datetimerangeconstantsinterface-constants-3574901.php` | `src/Drupal11/Rector/Deprecation/ReplaceDateTimeRangeConstantsRector.php` |
| `ReplaceEditorLoadRector` | D11 | **Significant** | [#3447794](https://www.drupal.org/node/3447794) | `rector/rules/replace-deprecated-editor-load-with-entity-storage-load-3447794.php` | `src/Drupal11/Rector/Deprecation/ReplaceEditorLoadRector.php` |
| `ReplaceEntityOriginalPropertyRector` | D11 | **Significant** | [#3571065](https://www.drupal.org/node/3571065) | `rector/rules/replace-deprecated-entity-original-magic-property-with-3571065.php` | `src/Drupal11/Rector/Deprecation/ReplaceEntityOriginalPropertyRector.php` |
| `ReplaceEntityReferenceRecursiveLimitRector` | D11 | **Significant** | [#3316878](https://www.drupal.org/node/3316878) † | `rector/rules/replace-deprecated-entityreferenceentityformatter-recursive-2940605.php` | `src/Drupal11/Rector/Deprecation/ReplaceEntityReferenceRecursiveLimitRector.php` |
| `ReplaceFieldgroupToFieldsetRector` | D11 | Minimal | [#3512254](https://www.drupal.org/node/3512254) | `rector/rules/replace-deprecated-type-fieldgroup-with-type-fieldset-3512254.php` | `src/Drupal11/Rector/Deprecation/ReplaceFieldgroupToFieldsetRector.php` |
| `ReplaceFileGetContentHeadersRector` | D11 | Minimal | [#3494126](https://www.drupal.org/node/3494126) | `rector/rules/replace-file-get-content-headers-with-fileinterface-3494126.php` | `src/Drupal11/Rector/Deprecation/ReplaceFileGetContentHeadersRector.php` |
| `ReplaceLocaleConfigBatchFunctionsRector` | D11 | **Significant** | [#3575254](https://www.drupal.org/node/3575254) | `rector/rules/replace-deprecated-locale-batch-functions-with-their-3575254.php` | `src/Drupal11/Rector/Deprecation/ReplaceLocaleConfigBatchFunctionsRector.php` |
| `ReplaceNodeAccessViewAllNodesRector` | D11 | Minimal | [#3038908](https://www.drupal.org/node/3038908) | `rector/rules/replace-deprecated-node-access-view-all-nodes-with-oo-3038908.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeAccessViewAllNodesRector.php` |
| `ReplaceNodeAddBodyFieldRector` | D11 | Minimal | [#3489266](https://www.drupal.org/node/3489266) | `rector/rules/replace-deprecated-node-add-body-field-with-createbodyfield-3489266.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeAddBodyFieldRector.php` |
| `ReplaceNodeModuleProceduralFunctionsRector` | D11 | Minimal | [#3571623](https://www.drupal.org/node/3571623) | `rector/rules/replace-deprecated-node-module-procedural-functions-with-oo-3571623.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeModuleProceduralFunctionsRector.php` |
| `ReplaceNodeSetPreviewModeRector` | D11 | Minimal | [#3538277](https://www.drupal.org/node/3538277) | `rector/rules/replace-deprecated-constants-with-nodepreviewmode-enum-in-3538277.php` | `src/Drupal11/Rector/Deprecation/ReplaceNodeSetPreviewModeRector.php` |
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
| `ViewsPluginHandlerManagerRector` | D11 | **Significant** | [#3566424](https://www.drupal.org/node/3566424) | `rector/rules/replace-deprecated-views-pluginmanager-and-views-3566424.php` | `src/Drupal11/Rector/Deprecation/ViewsPluginHandlerManagerRector.php` |

---

## Significant Changes

### 1. `ReplaceRequestTimeConstantRector` (Drupal10)

**Digest file:** `add-timeinterface-time-argument-to-plugin-constructor-3395986.php`

**Change:** The issue ID is shared but the two rules address entirely different deprecations. The digest (`AddTimeInterfaceToPluginConstructorsRector`) adds a `?TimeInterface $time` argument to `__construct()` overrides in subclasses of six Drupal plugin parent classes (`TimestampFormatter`, `views\argument\Date`, etc.), updating each `parent::__construct()` call to pass `$time`. The rector instead replaces occurrences of the `REQUEST_TIME` constant with `\Drupal::time()->getRequestTime()` by visiting `ConstFetch` nodes. The two rules share a Drupal issue number but implement completely unrelated transformations. No code from the digest was reused.

```php
// Digest — adds ?TimeInterface parameter to plugin __construct() overrides
final class AddTimeInterfaceToPluginConstructorsRector extends AbstractRector
{
    public function getNodeTypes(): array { return [Class_::class]; }
    // ...
}

// Rector — replaces REQUEST_TIME constant with method call
final class ReplaceRequestTimeConstantRector extends AbstractRector
{
    public function getNodeTypes(): array { return [Node\Expr\ConstFetch::class]; }
    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node, 'REQUEST_TIME')) { return null; }
        $staticCall = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'time');
        return new Node\Expr\MethodCall($staticCall, 'getRequestTime');
    }
}
```

---

### 2. `NodeStorageDeprecatedMethodsRector` (Drupal11)

**Digest file:** `replace-deprecated-nodestorage-revisionids-and-3396062.php`

**Change:** The digest handled only `revisionIds()` and `userRevisionIds()`, visiting only `MethodCall` nodes. The rector adds handling for `countDefaultLanguageRevisions()`, which has no replacement and must be removed entirely. This required registering `Expression::class` as a second node type and returning `NodeVisitor::REMOVE_NODE` when the expression statement wraps that call.

```php
// Digest — only MethodCall nodes
public function getNodeTypes(): array
{
    return [MethodCall::class];
}
// No handling for countDefaultLanguageRevisions()

// Rector — adds Expression node type and removal
public function getNodeTypes(): array
{
    return [Node\Expr\MethodCall::class, Node\Stmt\Expression::class];
}

if ($node instanceof Node\Stmt\Expression) {
    $methodCall = $node->expr;
    if ($this->getName($methodCall->name) === 'countDefaultLanguageRevisions') {
        return NodeVisitor::REMOVE_NODE;
    }
}
```

---

### 3. `RemoveAutomatedCronSubmitHandlerRector` (Drupal11)

**Digest file:** `remove-deprecated-automated-cron-settings-submit-calls-and-3566768.php`

**Change:** The digest registered two separate rules: the custom `RemoveAutomatedCronSettingsSubmitHandlerRector` class (which removed `$form['#submit'][] = 'automated_cron_settings_submit'` array-append assignments) plus a comment pointing to `RemoveFuncCallRector` to handle direct `automated_cron_settings_submit($form, $form_state)` function calls. The rector implements only the array-append removal, omitting the direct function-call case. The class was also renamed from `RemoveAutomatedCronSettingsSubmitHandlerRector` to `RemoveAutomatedCronSubmitHandlerRector`.

---

### 4. `RemoveCacheExpireOverrideRector` (Drupal11)

**Digest file:** `remove-deprecated-cacheexpire-overrides-from-views-3576556.php`

**Change:** The digest's `PARENT_SHORT_NAMES` listed `CachePluginBase`, `Time`, `Tag`, and `None`, and its `isCachePluginBaseSubclass()` used `str_ends_with($parentName, '\\' . $short)` for namespace-relative matching. It then fell back to a PHPStan `isSuperTypeOf()->yes()` check using an inline `\PHPStan\Type\ObjectType`.

The rector adds a separate `PARENT_FQCNS` constant listing all four fully-qualified names (`CachePluginBase`, `Time`, `Tag`, `None`), and restructures `isCachePluginBaseSubclass()` to first iterate `PARENT_FQCNS` for exact FQCN matches, then only applies short-name matching when `!str_contains($parentName, '\\')` (i.e., bare unqualified names). The PHPStan fallback uses the imported `ObjectType` class rather than an inline FQN. This prevents a namespace-relative name like `cache\None` from accidentally matching the short name `None` via `str_ends_with`.

```php
// Digest — single PARENT_SHORT_NAMES list, str_ends_with for all cases
private const PARENT_SHORT_NAMES = ['CachePluginBase', 'Time', 'Tag', 'None'];
// ...
foreach (self::PARENT_SHORT_NAMES as $short) {
    if ($parentName === $short || str_ends_with($parentName, '\\' . $short)) {
        return true;
    }
}

// Rector — separate PARENT_FQCNS constant, short-name check restricted to unqualified names
private const PARENT_FQCNS = [
    'Drupal\views\Plugin\views\cache\CachePluginBase',
    'Drupal\views\Plugin\views\cache\Time',
    'Drupal\views\Plugin\views\cache\Tag',
    'Drupal\views\Plugin\views\cache\None',
];

foreach (self::PARENT_FQCNS as $fqcn) {
    if ($parentName === $fqcn) { return true; }
}
if (!str_contains($parentName, '\\')) {
    foreach (self::PARENT_SHORT_NAMES as $short) {
        if ($parentName === $short) { return true; }
    }
}
```

---

### 5. `RemoveConfigSaveTrustedDataArgRector` + `RemoveTrustDataCallRector` (Drupal11)

**Digest file:** `remove-deprecated-trusted-data-concept-from-drupal-config-3347842.php` (one file, split into two rector classes)

**Change:** The digest defined a single class (`RemoveTrustedDataConceptRector`) that handled both patterns in one `refactor()` method: it removed the boolean arg from `->save(TRUE/FALSE)` and removed `->trustData()` from fluent chains, both without any type guard.

The rector splits these into two focused classes. `RemoveConfigSaveTrustedDataArgRector` handles only `Config::save(TRUE/FALSE)` and adds a `Drupal\Core\Config\Config` ObjectType guard. `RemoveTrustDataCallRector` handles only `->trustData()` chain removal and adds a `Drupal\Core\Config\Entity\ConfigEntityInterface` ObjectType guard. Both additions prevent false positives on unrelated classes with `save()` or `trustData()` methods.

```php
// Digest — no type guards, single class
if ($this->isName($node->name, 'save') && count($node->args) === 1 ...) {
    // remove boolean arg, no ObjectType check
}
if ($this->isName($node->name, 'trustData') && count($node->args) === 0) {
    return $node->var; // no ObjectType check
}

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

### 6. `RemoveHandlerBaseDefineExtraOptionsRector` (Drupal11)

**Digest file:** `remove-overrides-of-deprecated-handlerbase-3485084.php`

**Change:** The digest (`RemoveDefineExtraOptionsOverrideRector`) relied solely on PHPStan's `isObjectType()` against `HandlerBase` and explicitly excluded the `HandlerBase` class itself by checking the short class name.

The rector takes a different approach: it uses a multi-strategy `isHandlerBaseSubclass()` helper that first checks `HANDLER_BASE_FQCN` for exact match, then iterates a `PARENT_SHORT_NAMES` constant (listing six short names: `HandlerBase`, `FieldHandlerBase`, `FilterPluginBase`, `SortPluginBase`, `ArgumentPluginBase`, `RelationshipPluginBase`) with `str_ends_with` support, and only falls back to `isObjectType()` as a last resort. This makes the rector more resilient when PHPStan's type resolution is unavailable. The `HandlerBase`-exclusion logic from the digest is absent (not needed since the detection now targets subclasses more precisely).

```php
// Digest — relies primarily on isObjectType, excludes HandlerBase by name
if (!$this->isObjectType($node->extends, new ObjectType('Drupal\\views\\Plugin\\views\\HandlerBase'))) {
    return null;
}
if ($node->name instanceof Identifier && $node->name->toString() === 'HandlerBase') {
    return null; // skip HandlerBase itself
}

// Rector — explicit PARENT_SHORT_NAMES list as primary detection
private const PARENT_SHORT_NAMES = [
    'HandlerBase', 'FieldHandlerBase', 'FilterPluginBase',
    'SortPluginBase', 'ArgumentPluginBase', 'RelationshipPluginBase',
];
foreach (self::PARENT_SHORT_NAMES as $short) {
    if ($parentName === $short || str_ends_with($parentName, '\\'.$short)) {
        return true;
    }
}
```

---

### 7. `RemoveModuleHandlerAddModuleCallsRector` (Drupal11)

**Digest file:** `remove-deprecated-modulehandlerinterface-addmodule-and-3528899.php`

**Change:** The digest checked only `ModuleHandlerInterface`. The rector also checks the concrete `ModuleHandler` class, covering cases where the variable is typed as the concrete implementation rather than the interface.

```php
// Digest — interface only
if ($this->isObjectType($methodCall->var, new ObjectType('Drupal\\Core\\Extension\\ModuleHandlerInterface'))) {
    return NodeVisitor::REMOVE_NODE;
}

// Rector — interface + concrete class
foreach (['Drupal\Core\Extension\ModuleHandlerInterface', 'Drupal\Core\Extension\ModuleHandler'] as $class) {
    if ($this->isObjectType($methodCall->var, new ObjectType($class))) {
        $isModuleHandler = true;
        break;
    }
}
```

---

### 8. `RemoveModuleHandlerDeprecatedMethodsRector` (Drupal11)

**Digest file:** `remove-deprecated-modulehandlerinterface-writecache-and-3442009.php`

**Change:** Both rules handle `writeCache()` removal and `getHookInfo()` → `[]` replacement identically in terms of transformation output. The key difference is how standalone `getHookInfo()` expression statements are handled. The digest left them in place as a bare `[];` expression (replacing the method call with an empty array literal as a statement, which is harmless dead code). The rector removes standalone `getHookInfo()` expression statements entirely via `NodeVisitor::REMOVE_NODE`, treating them the same as `writeCache()` calls. The rector also refactors the repeated type-check logic into a private `isModuleHandlerMethodCall()` helper, which the digest inlined.

```php
// Digest — standalone getHookInfo() becomes bare []; (not removed)
if ($node instanceof Expression && $node->expr instanceof MethodCall) {
    if ($this->isName($call->name, 'writeCache') && $this->isObjectType(...)) {
        return NodeVisitor::REMOVE_NODE;
    }
    // getHookInfo() as statement: falls through to MethodCall branch → returns new Array_()
}

// Rector — standalone getHookInfo() is also removed
if ($node instanceof Expression && $node->expr instanceof MethodCall) {
    if ($this->isModuleHandlerMethodCall($call, 'writeCache')
        || $this->isModuleHandlerMethodCall($call, 'getHookInfo')
    ) {
        return NodeVisitor::REMOVE_NODE;
    }
}
```

---

### 9. `RemoveRootFromConvertDbUrlRector` (Drupal11)

**Digest file:** `remove-deprecated-string-root-from-database-3522513.php`

**Change:** The digest (`RemoveRootFromConvertDbUrlToConnectionInfoRector`) recognized `String_`, `PropertyFetch`, `NullsafePropertyFetch`, `FuncCall`, `StaticPropertyFetch`, and `MethodCall` as second-argument forms that should be stripped. The rector preserves all of these and adds the same logic, but also renames the class from `RemoveRootFromConvertDbUrlToConnectionInfoRector` to `RemoveRootFromConvertDbUrlRector`. Functionally the two implementations are equivalent; the differences are limited to class name, namespace, and the import style (inline `\PhpParser\Node\Expr\StaticPropertyFetch` vs imported `StaticPropertyFetch`). This entry is classified **Significant** only because the class was renamed and the rector adds the `StaticPropertyFetch` and `MethodCall` types explicitly as named imports where the digest used inline FQNs.

---

### 10. `RemoveTwigNodeTransTagArgumentRector` (Drupal11)

**Digest file:** `remove-deprecated-tag-argument-from-twignodetrans-3473440.php`

**Change:** Two differences in argument-removal logic and class matching. The digest (`RemoveTwigNodeTransTagArgRector`) used `TARGET_CLASS = 'Drupal\\Core\\Template\\TwigNodeTrans'` with `$this->isName()`, matched only on the FQCN, checked `isset($node->args[5])`, and used `array_splice($node->args, 5)` which removes index 5 and everything beyond it.

The rector checks both the FQCN `Drupal\Core\Template\TwigNodeTrans` and the short name `TwigNodeTrans` (using `$this->getName($node->class)` and comparing with `!==`), restricts the transformation to exactly `count($node->args) === 6`, and uses `array_pop($node->args)` to remove only the last argument. The short-name support broadens coverage to files that import the class via `use`. The `count() === 6` check is more restrictive than `isset([5])` (which fires for 7+ args too).

```php
// Digest — FQCN only, isset check, array_splice
if (!$this->isName($node->class, self::TARGET_CLASS)) { return null; }
if (!isset($node->args[5])) { return null; }
array_splice($node->args, 5);

// Rector — FQCN or short name, exact count, array_pop
if ($className !== 'TwigNodeTrans' && $className !== 'Drupal\Core\Template\TwigNodeTrans') {
    return null;
}
if (count($node->args) !== 6) { return null; }
array_pop($node->args);
```

---

### 11. `RenameStopProceduralHookScanRector` (Drupal11)

**Digest file:** `rename-stopproceduralhookscan-attribute-to-3495943.php`

**Change:** The digest was a config-only snippet (no class body) that delegated to the built-in `RenameClassRector` with a single `StopProceduralHookScan` → `ProceduralHookScanStop` mapping. `RenameClassRector` rewrites all class references but does not specifically handle the `use` statement and attribute usage site independently — it also risks rewriting unrelated references in class bodies that happen to share the same short name.

The rector implements a full custom rule visiting two distinct node types (`UseUse` and `Attribute`) and handles them separately: it rewrites the `use` statement's `name` to the new FQCN and rewrites `#[StopProceduralHookScan]` attribute nodes (both FQCN and short-name forms) to `#[ProceduralHookScanStop]`. This is more precise and avoids the side effects of `RenameClassRector`.

---

### 12. `ReplaceCommentManagerGetCountNewCommentsRector` (Drupal11)

**Digest file:** `replace-deprecated-commentmanagerinterface-3543035.php`

**Note:** The rector's `@see` references issue `#3551729`; the digest file uses `#3543035`. Both reference the same deprecation.

**Change:** The digest extended `AbstractRector` directly with a plain `refactor()` method. The rector extends `AbstractDrupalCoreRector` and wraps the logic in `refactorWithConfiguration()`, enabling version-gated activation via `DrupalIntroducedVersionConfiguration('11.3.0')`. The transformation logic itself (building `\Drupal::service(HistoryManager::class)->getCountNewComments()`) is identical.

```php
// Digest
final class CommentManagerGetCountNewCommentsRector extends AbstractRector
{
    public function refactor(Node $node): ?Node { ... }
}

// Rector
final class ReplaceCommentManagerGetCountNewCommentsRector extends AbstractDrupalCoreRector
{
    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node { ... }
    // getRuleDefinition() uses ConfiguredCodeSample with DrupalIntroducedVersionConfiguration('11.3.0')
}
```

---

### 13. `ReplaceEditorLoadRector` (Drupal11)

**Digest file:** `replace-deprecated-editor-load-with-entity-storage-load-3447794.php`

**Change:** The digest (`EditorLoadDeprecationRector`) built the replacement AST manually with inline `new StaticCall(new FullyQualified('Drupal'), 'entityTypeManager', [])`, `new MethodCall(...)`, and used `$node->args[0] ?? new Node\Arg(new Node\Expr\ConstFetch(new Name('null')))` to handle a missing first argument. The rector uses `$this->nodeFactory->createStaticCall()` and `$this->nodeFactory->createMethodCall()` helpers, which is cleaner and more idiomatic for drupal-rector. The rector also adds an explicit `count($node->args) !== 1` guard that the digest lacked (the digest silently substituted null). The class was renamed from `EditorLoadDeprecationRector`.

---

### 14. `ReplaceEntityOriginalPropertyRector` (Drupal11)

**Digest file:** `replace-deprecated-entity-original-magic-property-with-3571065.php`

**Change:** The digest (`EntityOriginalPropertyToMethodRector`) handled `PropertyFetch` and `Assign` nodes only. For `Assign`, it added an `EntityInterface` ObjectType check on the `getOriginal()` MethodCall var, but for the `PropertyFetch` branch it relied only on the `$this->original`-skip check plus `isObjectType(EntityInterface)`.

The rector adds `NullsafePropertyFetch` as a third registered node type and handles `$entity?->original` → `$entity?->getOriginal()` by returning a new `NullsafeMethodCall`. The `Assign` branch in the rector drops the secondary `EntityInterface` check (since by that pass the `PropertyFetch` was already transformed to `getOriginal()`, so the type check is redundant). The class was renamed from `EntityOriginalPropertyToMethodRector`.

```php
// Digest — only PropertyFetch and Assign
public function getNodeTypes(): array { return [PropertyFetch::class, Assign::class]; }

// Rector — adds NullsafePropertyFetch
public function getNodeTypes(): array
{
    return [PropertyFetch::class, NullsafePropertyFetch::class, Assign::class];
}
// Handles $entity?->original → $entity?->getOriginal()
if ($node instanceof NullsafePropertyFetch) {
    if ($this->isName($node->name, 'original') && ...) {
        return new NullsafeMethodCall($node->var, 'getOriginal');
    }
}
```

---

### 15. `ReplaceEntityReferenceRecursiveLimitRector` (Drupal11)

**Digest file:** `replace-deprecated-entityreferenceentityformatter-recursive-2940605.php`

**Note:** The rector's `@see` references issue `#3316878`; the digest file uses `#2940605`. Both refer to the same deprecation.

**Change:** The digest (`RemoveEntityReferenceRecursiveLimitConstantRector`) used `isObjectType()` for FQCN matching and additionally handled `static::`, `self::`, and `parent::` class-constant fetches within subclasses. The rector simplifies this by maintaining a `TARGET_CLASSES` constant (listing both the short name `EntityReferenceEntityFormatter` and the FQCN) and matching via `$this->isName($node->class, $class)` — which can match both short names (resolved by Rector via `use` imports) and FQCNs, but does not handle `static::`/`self::`/`parent::`. The rector's approach is simpler but slightly less complete.

```php
// Digest — handles static/self/parent via isObjectType
if (in_array((string) $class, ['static', 'self', 'parent'], true)) {
    if ($this->isObjectType($node, new ObjectType(self::DEPRECATED_CLASS))) {
        return new LNumber(20);
    }
}

// Rector — TARGET_CLASSES with isName(), no static/self/parent handling
private const TARGET_CLASSES = [
    'EntityReferenceEntityFormatter',
    'Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter',
];
foreach (self::TARGET_CLASSES as $class) {
    if ($this->isName($node->class, $class)) { return new Int_(20); }
}
```

---

### 16. `ReplaceLocaleConfigBatchFunctionsRector` (Drupal11)

**Digest file:** `replace-deprecated-locale-batch-functions-with-their-3575254.php`

**Change:** The digest was a config-only snippet delegating to the built-in `RenameFunctionRector` with a two-entry rename map. The rector implements a full custom `FuncCall`-visiting rule with a `RENAME_MAP` constant, providing testability, explicit control, and compatibility with the drupal-rector test infrastructure.

---

### 17. `ReplacePdoFetchConstantsRector` (Drupal11)

**Digest file:** `replace-removed-mysql-pgsql-sqlite-driver-query-subclass-3525077.php`

**Change:** The issue ID is shared but the two rules address entirely different aspects of the same issue. The digest was a config-only snippet delegating to `RenameClassRector` to remap nine deprecated driver-specific query subclasses (e.g. `Drupal\mysql\Driver\Database\mysql\Delete`) to their `Drupal\Core\Database\Query\*` base equivalents.

The rector is a fully custom rule that converts `PDO::FETCH_*` constants to `FetchAs` enum cases in Drupal Database API calls. It visits `MethodCall` nodes for `setFetchMode()`, `fetch()`, `fetchAll()`, `fetchAllAssoc()`, and `ArrayItem` nodes for `'fetch'` array keys. It also guards against accidentally rewriting `PDO::FETCH_*` on native PDO methods by checking whether the callee is `getClientStatement()` or `getClientConnection()`. No code from the digest was reused.

---

### 18. `ReplaceSessionManagerDeleteRector` (Drupal11)

**Digest file:** `replace-deprecated-sessionmanager-delete-with-3577376.php`

**Change:** The digest extended `AbstractRector` directly and used the PHPStan `$sessionManagerType->isSuperTypeOf($callerType)->yes()` pattern to check the caller type. The rector extends `AbstractDrupalCoreRector` and uses `refactorWithConfiguration()` with `DrupalIntroducedVersionConfiguration('11.4.0')`, enabling version-gated activation. The type check was changed to `$this->isObjectType($node->var, new ObjectType('Drupal\Core\Session\SessionManager'))`, which is the standard Rector API.

```php
// Digest — plain refactor(), PHPStan isSuperTypeOf
final class ReplaceSessionManagerDeleteRector extends AbstractRector
{
    public function refactor(Node $node): ?Node {
        $callerType = $this->getType($node->var);
        if (!$sessionManagerType->isSuperTypeOf($callerType)->yes()) { return null; }
    }
}

// Rector — refactorWithConfiguration(), isObjectType
final class ReplaceSessionManagerDeleteRector extends AbstractDrupalCoreRector
{
    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node {
        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Session\SessionManager'))) { return null; }
    }
}
```

---

### 19. `StatementPrefetchIteratorFetchColumnRector` (Drupal11)

**Digest file:** `replace-deprecated-statementprefetchiterator-fetchcolumn-3490200.php`

**Change:** The digest was a config-only snippet delegating to the built-in `RenameMethodRector` with a single `StatementPrefetchIterator::fetchColumn → fetchField` mapping. The rector implements a full custom `MethodCall`-visiting rule with an explicit `StatementPrefetchIterator` ObjectType check, making the transformation testable and safe against accidental rewrites of unrelated `fetchColumn()` methods.

---

### 20. `UseEntityTypeHasIntegerIdRector` (Drupal11)

**Digest file:** `replace-deprecated-entity-type-integer-id-helpers-with-3566801.php`

**Change:** The digest used a `TARGET_PARENTS` list with three FQCNs and a shared `isInTargetParent()` helper that called `isObjectType()` for all three, covering all three method patterns under one broad type check. It also handled the `hasIntegerId($entityType)` pattern directly.

The rector replaces the broad `TARGET_PARENTS` approach with a `METHOD_OWNER_CLASS` map that pairs each method name to its specific declaring class (`entityTypeSupportsComments` → `CommentTypeForm`, `hasIntegerId` → `OverridesSectionStorage`). The `getEntityTypeIdKeyType` comparison pattern uses a dedicated `GET_ENTITY_TYPE_ID_KEY_TYPE_CLASS` constant. This means each method is checked against only its own class rather than all three parent classes, making false positives less likely.

```php
// Digest — shared TARGET_PARENTS, checks all three for every method
private const TARGET_PARENTS = [
    'Drupal\\Core\\Entity\\Routing\\DefaultHtmlRouteProvider',
    'Drupal\\comment\\CommentTypeForm',
    'Drupal\\layout_builder\\Plugin\\SectionStorage\\OverridesSectionStorage',
];
private function isInTargetParent(Node $node): bool {
    foreach (self::TARGET_PARENTS as $parent) {
        if ($this->isObjectType($node, new ObjectType($parent))) { return true; }
    }
}

// Rector — METHOD_OWNER_CLASS pairs each method to its specific owner
private const METHOD_OWNER_CLASS = [
    'entityTypeSupportsComments' => 'Drupal\comment\CommentTypeForm',
    'hasIntegerId' => 'Drupal\layout_builder\Plugin\SectionStorage\OverridesSectionStorage',
];
if (!$this->isObjectType($node->var, new ObjectType(self::METHOD_OWNER_CLASS[$name]))) {
    return null;
}
```

---

### 21. `ViewsPluginHandlerManagerRector` (Drupal11)

**Digest file:** `replace-deprecated-views-pluginmanager-and-views-3566424.php`

**Change:** The digest used `$this->isObjectType($node->class, new \PHPStan\Type\ObjectType('Drupal\\views\\Views'))` to match the static call class — which is incorrect for static call class nodes (the `class` property is a `Name`, not an object expression, so `isObjectType` does not apply). The rector replaces this with `$this->isName($node->class, 'Drupal\views\Views')`, which is the correct API for matching a class name on a `StaticCall` node. The transformation logic (string-literal vs dynamic argument) is identical.

```php
// Digest — incorrect isObjectType on a Name node
if (!$this->isObjectType($node->class, new \PHPStan\Type\ObjectType('Drupal\\views\\Views'))) {
    return null;
}

// Rector — correct isName for StaticCall class
if (!$this->isName($node->class, 'Drupal\views\Views')) {
    return null;
}
```

---

## Minimal Changes

These rectors are functionally equivalent to their digest counterparts. Differences are limited to: namespace declarations, proper `use` imports (replacing inline backslash-prefixed FQNs), `declare(strict_types=1)` placement, class renaming to match drupal-rector conventions, and minor wording in `getRuleDefinition()`.

| Rector class | Notable structural differences from digest |
|---|---|
| `ReplaceModuleHandlerGetNameRector` | Namespace + `AbstractDrupalCoreRector` wrapping + `DrupalIntroducedVersionConfiguration('10.3.0')`; logic identical to digest |
| `ReplaceRebuildThemeDataRector` | Namespace + `AbstractDrupalCoreRector` wrapping + `DrupalIntroducedVersionConfiguration('10.3.0')`; the `ThemeHandlerInterface` ObjectType check and `!empty($node->args)` guard were already present in the 0503 digest, so logic is identical |
| `ErrorCurrentErrorHandlerRector` | Namespace + imports only; `ObjectType` imported vs inline `\PHPStan\Type\ObjectType`; adds `assert($node instanceof StaticCall)` |
| `FileSystemBasenameToNativeRector` | Namespace + imports only; type-check API changed from `isSuperTypeOf()->yes()` to `isObjectType()` (semantically equivalent) |
| `LoadAllIncludesRector` | Namespace + imports only; logic and structure identical |
| `MigrateSqlGetMigrationPluginManagerRector` | Namespace + imports only; 0503 digest already uses `isObjectType(Sql)` as positive guard — identical to rector |
| `PluginBaseIsConfigurableRector` | Namespace + imports only; 0503 digest already has `isObjectType(PluginBase)` guard — identical to rector; class renamed from `ReplacePluginBaseIsConfigurableRector` |
| `RemoveLinkWidgetValidateTitleElementRector` | Namespace + imports only; logic identical |
| `RemoveSetUriCallbackRector` | Namespace + imports only; 0503 digest already has `EntityTypeInterface` ObjectType guards — identical to rector |
| `RemoveStateCacheSettingRector` | Namespace + imports only; logic identical |
| `RemoveUpdaterPostInstallMethodsRector` | Namespace + imports only; backslash escaping in `UPDATER_BASE_CLASSES` normalized (digest used escaped `\\`) |
| `ReplaceAlphadecimalToIntNullRector` | Namespace + imports only; class renamed from `AlphadecimalToIntNullOrEmptyRector`; inline `\PHPStan` and `\PhpParser` FQNs replaced with imports |
| `ReplaceCommentUriRector` | Namespace + imports only; class renamed from `CommentUriToPermalinkRector`; arg count check changed from `!== 1` (digest) to `< 1` (rector) |
| `ReplaceDateTimeRangeConstantsRector` | Namespace + imports only; class renamed from `ReplaceDatetimeDeprecatedApisRector`; inline `Config\RectorConfig` use removed; logic identical |
| `ReplaceFieldgroupToFieldsetRector` | Namespace + imports only; class renamed from `FieldgroupToFieldsetRector`; logic identical |
| `ReplaceFileGetContentHeadersRector` | Namespace + imports only; class renamed from `FileGetContentHeadersRector`; adds `assert()`; logic identical |
| `ReplaceNodeAccessViewAllNodesRector` | Namespace + imports only; class renamed from `NodeAccessViewAllNodesRector`; logic identical |
| `ReplaceNodeAddBodyFieldRector` | Namespace + imports only; class renamed from `NodeAddBodyFieldRector`; logic identical |
| `ReplaceNodeModuleProceduralFunctionsRector` | Namespace + imports only; class renamed from `ReplaceDeprecatedNodeFunctionsRector`; logic identical |
| `ReplaceNodeSetPreviewModeRector` | Namespace + imports only; class renamed from `NodeSetPreviewModeRector`; 0503 digest already has `NodeTypeInterface` ObjectType guard — logic identical |
| `ReplaceRecipeRunnerInstallModuleRector` | Namespace + imports only; class renamed from `RecipeRunnerInstallModuleRector`; logic identical |
| `ReplaceSessionWritesWithRequestSessionRector` | Namespace + imports only; class renamed from `SessionSuperGlobalToRequestSessionRector`; logic identical |
| `ReplaceSystemPerformanceGzipKeyRector` | Namespace + imports only; class renamed from `SystemPerformanceGzipToCompressRector`; logic identical |
| `ReplaceThemeGetSettingRector` | Namespace + imports only; logic identical |
| `ReplaceUserSessionNamePropertyRector` | Namespace + imports only; class renamed from `UserSessionNamePropertyToGetAccountNameRector`; adds `UserSession` ObjectType check and `$this->name` skip guard (digest had neither) |
| `ReplaceViewsProceduralFunctionsRector` | Namespace + imports only; class renamed from `ReplaceDeprecatedViewsFunctionsRector`; logic identical |
| `StripMigrationDependenciesExpandArgRector` | Namespace + imports only; class renamed from `RemoveMigrationDependenciesExpandArgRector`; type-check API changed from `isSuperTypeOf()->yes()` to `isObjectType()` (semantically equivalent) |
| `RemoveViewsRowCacheKeysRector` | Namespace + imports only; 0503 digest already has `CachePluginBase` ObjectType guard; logic equivalent (helper inlined vs private method) |

---

## Notes on Digest File Mapping

### One digest file → two rector files
`remove-deprecated-trusted-data-concept-from-drupal-config-3347842.php` defined a single class (`RemoveTrustedDataConceptRector`) handling both `->save(TRUE)` and `->trustData()` patterns. The rector project splits these into two separate class files — `RemoveConfigSaveTrustedDataArgRector` and `RemoveTrustDataCallRector` — consistent with the project's one-class-per-file convention. Both rector classes add ObjectType guards absent from the single digest class.

### Issue number mismatches
Three rectors reference a different `@see` issue number than the digest filename's suffix. In each case the transformation is the same; the numbers refer to different nodes in the same deprecation issue thread:

| Rector | Rector `@see` | Digest filename issue | Notes |
|---|---|---|---|
| `RemoveViewsRowCacheKeysRector` | `#3564958` | `#3564937` | `3564958` is the change record; `3564937` is the original issue |
| `ReplaceCommentManagerGetCountNewCommentsRector` | `#3551729` | `#3543035` | `3543035` is the original issue; `3551729` is the related change record |
| `ReplaceEntityReferenceRecursiveLimitRector` | `#3316878` | `#2940605` | `2940605` is the older issue; `3316878` is the more recent change record |
