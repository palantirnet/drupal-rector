# Config-only recipe template

All config-only recipes share the same 5-step structure. Each recipe file specialises
the exact config syntax and fixture shape for one generic rector.

---

## The 5 steps (same for every config-only recipe)

### Step 1 — Identify the config file

| introducedVersion | File |
|---|---|
| 11.4.x | `config/drupal-11/drupal-11.4-deprecations.php` |
| 11.3.x | `config/drupal-11/drupal-11.3-deprecations.php` |
| 11.2.x | `config/drupal-11/drupal-11.2-deprecations.php` |
| 11.1.x | `config/drupal-11/drupal-11.1-deprecations.php` |
| 11.0.x | `config/drupal-11/drupal-11.0-deprecations.php` |

### Step 2 — Add the config entry

See the specific recipe for the exact entry syntax.
Add the `use` statement for the configuration value object if it is not yet imported.

### Step 3 — Add a fixture to the generic rector's test directory

Path: `tests/src/Rector/Deprecation/{{GenericRectorName}}/fixture/{{descriptive-name}}.php.inc`

Format (no BC wrapper — generic rectors do not produce one):
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
        tests/src/Rector/Deprecation/{{GenericRectorName}}/
git commit -m "feat(Drupal11): Add {{GenericRectorName}} config for issue #{{issueNumber}}"
```

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

Values: `{{introducedVersion}}`, `{{functionName}}`, `'{{serviceId}}'`, `{{methodName}}`

```php
new FunctionToServiceConfiguration('{{introducedVersion}}', '{{functionName}}', '{{serviceId}}', '{{methodName}}'),
```

`serviceId` is a dotted string like `'filter.format_repository'`, NOT a class name.
Use `func-to-class-service-bc.md` instead when the service is identified by `Fqcn::class`.
Fixture "after": `\Drupal::service('{{serviceId}}')->{{methodName}}(args)`
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

Values: `{{GLOBAL_CONST}}`, `{{TargetClass}}`, `{{CONST_NAME}}`

```php
new ConstantToClassConfiguration('{{GLOBAL_CONST}}', '{{TargetClass}}', '{{CONST_NAME}}'),
```

No BC wrapping.
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
