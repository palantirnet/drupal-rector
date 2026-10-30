# Config-only recipe template

All config-only recipes share the same 5-step structure. Each recipe file specialises
the exact config syntax and fixture shape for one generic rector.

---

## The 5 steps (same for every config-only recipe)

### Step 1 — Identify the config file

The file is named after the minor the deprecation was **introduced** in:
`config/drupal-<major>/drupal-<major>.<minor>-deprecations.php`, e.g. 11.4.x →
`config/drupal-11/drupal-11.4-deprecations.php`. If that minor has no file yet, create it
(copy an existing one) and add its constant to `src/Set/Drupal<major>SetList.php` and the
`drupal-<major>-all-deprecations.php` aggregate.

A `RenameClassRector` entry whose target class only exists from that minor on is **breaking**:
it goes in `drupal-<major>.<minor>-breaking.php` instead.

Also work out the bound now (`../version-bounds.md` §2): `>={{introducedVersion}} <{{majorAfterRemoval}}.0.0`,
e.g. deprecated in 11.4.0, removed in 13.0.0 → `>=11.4.0 <14.0.0`.

### Step 2 — Add the config entry (two files)

See the specific recipe for the exact entry syntax.

1. **Per-minor file** from Step 1: add the entry inside the matching
   `$rectorConfig->ruleWithConfiguration()` block. Add the `use` statement for the
   configuration value object if it is not yet imported.
2. **`config/composer-based.php`**: add the same entry as its **own** call under the matching
   `// Drupal X.Y` (or `// Drupal X.Y (breaking)`) heading, copying the comment block above it:
   ```php
   // https://www.drupal.org/node/{{issueNumber}}
   // {{deprecatedSymbol}} deprecated in drupal:{{introducedVersion}}, removed in drupal:{{removedVersion}}.
   $rectorConfig->ruleWithConfigurationComposerVersionBound({{GenericRectorName}}::class, [
       {{configEntry}}
   ], 'drupal/core', '>={{introducedVersion}} <{{majorAfterRemoval}}.0.0');
   ```
   One call per deprecation — do not append to another entry's call, its bound may differ.

### Step 3 — Add a fixture to the generic rector's test directory

Path: `tests/src/Rector/Deprecation/{{GenericRectorName}}/fixture/{{descriptive-name}}.php.inc`

Format below is for the unwrapped generics (`FunctionCallRemovalRector`,
`MethodToMethodWithCheckRector`, `ClassConstantToClassConstantRector`, `RenameClassRector`).
The generics that take an `introducedVersion` (`FunctionToStaticRector`, `FunctionToServiceRector`,
`FunctionToFirstArgMethodRector`, `DrupalServiceRenameRector`, `ConstantToClassConstantRector`)
are BC-wrapped for versions >= 10.1.0: their "after" is the
`\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '…', fn() => <new>, fn() => <old>)`
form — copy the exact shape from a sibling fixture or from the first failing test diff.
```
<?php

{{beforeCode}};
?>
-----
<?php

{{afterCode}};
?>
```

### Step 4 — Register the fixture in the generic rector's test config

File: `tests/src/Rector/Deprecation/{{GenericRectorName}}/config/configured_rule.php`

Add the configuration entry that was added to the deprecations config in Step 2.

### Step 5 — Run quality checks and commit

```bash
ddev composer fix-style
ddev composer phpstan
vendor/bin/phpunit tests/src/Rector/Deprecation/{{GenericRectorName}}/
git add config/drupal-11/drupal-11.{{X}}-deprecations.php \
        config/composer-based.php \
        tests/src/Rector/Deprecation/{{GenericRectorName}}/
git commit -m "feat: add {{GenericRectorName}} config for #{{issueNumber}}"
```

A `drupalRector.composerBasedSetCoverage` PHPStan error means Step 2.2 was skipped. Add a
`CHANGELOG.md` entry under `[Unreleased] / ### Added` (a separate `docs:` commit is fine).

---

## Config entry syntax by generic rector

### FunctionCallRemovalRector

Values: `{{functionName}}`

```php
new FunctionCallRemovalConfiguration('{{functionName}}'),
```

No replacement — the entire call statement is deleted.
Fixture "after" is the code with the statement removed entirely.

---

### FunctionToStaticRector

Values: `{{introducedVersion}}`, `{{functionName}}`, `{{ClassName}}`, `{{methodName}}`

```php
new FunctionToStaticConfiguration('{{introducedVersion}}', '{{functionName}}', '{{ClassName}}', '{{methodName}}'),
```

Optional 5th argument: arg reorder map, e.g. `[0 => 1, 1 => 0]` to swap the first two args.
Fixture "after": `{{ClassName}}::{{methodName}}(args)`
BC-wrapped (introduced >= 10.1.0).

---

### FunctionToServiceRector

Values: `{{introducedVersion}}`, `{{functionName}}`, `{{serviceId}}`, `{{methodName}}`

**String service ID** (dotted alias like `'file_system'`, `'renderer'`):
```php
new FunctionToServiceConfiguration('{{introducedVersion}}', '{{functionName}}', '{{serviceId}}', '{{methodName}}'),
```
Fixture "after": `\Drupal::service('{{serviceId}}')->{{methodName}}(args)`

**FQCN service ID** (class or interface name like `Drupal\node\NodeGrantsHelper`):
```php
new FunctionToServiceConfiguration('{{introducedVersion}}', '{{functionName}}', '{{fqcn}}', '{{methodName}}', true),
```
Fixture "after": `\Drupal::service(\{{fqcn}}::class)->{{methodName}}(args)`

Use `func-to-class-service-bc.md` only when the replacement has custom logic (arg-count dispatch, chained calls, method on first arg, etc.). For a plain 1-to-1 mapping, the 5th `true` argument handles it.

BC-wrapped (introduced >= 10.1.0).

---

### FunctionToFirstArgMethodRector

Values: `{{introducedVersion}}`, `{{functionName}}`, `{{methodName}}`

```php
new FunctionToFirstArgMethodConfiguration('{{introducedVersion}}', '{{functionName}}', '{{methodName}}'),
```

`{{functionName}}($obj, ...)` → `$obj->{{methodName}}(...remaining args)`
Fixture "after": `$firstArg->{{methodName}}(...)`
BC-wrapped (introduced >= 10.1.0).

---

### DrupalServiceRenameRector

Values: `{{introducedVersion}}`, `'{{oldServiceId}}'`, `'{{newServiceId}}'`

```php
new DrupalServiceRenameConfiguration('{{introducedVersion}}', '{{oldServiceId}}', '{{newServiceId}}'),
```

Fixture "after": `\Drupal::service('{{newServiceId}}')`
BC-wrapped (introduced >= 10.1.0).

---

### MethodToMethodWithCheckRector

Values: `{{ReceiverClass}}`, `{{oldMethod}}`, `{{newMethod}}`

```php
new MethodToMethodWithCheckConfiguration('{{ReceiverClass}}', '{{oldMethod}}', '{{newMethod}}'),
```

No `introducedVersion` — applies unconditionally, no BC wrapping.
`{{ReceiverClass}}` is the FQCN of the interface/class the receiver must be typed as.
Fixture "after": `$receiver->{{newMethod}}(args)`

---

### ClassConstantToClassConstantRector

Values: `{{OldClass}}`, `{{OLD_CONST}}`, `{{NewClass}}`, `{{NEW_CONST}}`

```php
new ClassConstantToClassConstantConfiguration('{{OldClass}}', '{{OLD_CONST}}', '{{NewClass}}', '{{NEW_CONST}}'),
```

No BC wrapping.
Fixture "after": `{{NewClass}}::{{NEW_CONST}}`

---

### ConstantToClassConstantRector

Values: `{{GLOBAL_CONST}}`, `{{TargetClass}}`, `{{CONST_NAME}}`, `{{introducedVersion}}`

```php
new ConstantToClassConfiguration('{{GLOBAL_CONST}}', '{{TargetClass}}', '{{CONST_NAME}}', '{{introducedVersion}}'),
```

`introducedVersion` is required. BC-wrapped (introduced >= 10.1.0) — the fixture "after" shows the
`DeprecationHelper::backwardsCompatibleCall()` form.
Fixture "after": `\{{TargetClass}}::{{CONST_NAME}}`

---

### RenameClassRector (Rector core)

Values: `{{OldFqcn}}`, `{{NewFqcn}}`

```php
$rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
    '{{OldFqcn}}' => '{{NewFqcn}}',
]);
```

Add `use Rector\Renaming\Rector\Name\RenameClassRector;` at the top of the config file.
No BC wrapping.
Fixture "after": all references to `{{OldFqcn}}` replaced with `{{NewFqcn}}`.
