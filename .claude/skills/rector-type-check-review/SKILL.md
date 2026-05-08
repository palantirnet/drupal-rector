---
name: rector-type-check-review
description: Reviews Drupal rector rules for type-specificity — ensures method calls, property accesses, and $this references are guarded by isObjectType() or equivalent before transforming. Use when checking rectors that match by name alone without verifying the owning class. Run on individual rectors or walk through the whole branch list.
argument-hint: "[RectorClassName or 'all' or leave empty to use checklist]"
allowed-tools: Read, Bash, Edit, Write, Glob
---

# Rector Type-Check Review

Rector rules that match a method call (`->foo()`), property access (`->bar`), or `$this` usage by *name only* — without verifying the owning class or interface — are a false-positive risk. Any unrelated class that happens to have the same method/property name will be transformed.

This skill evaluates one or more rectors for this issue and, when a problem is found, proposes or implements the fix.

## The Problem, Summarised

| Pattern | What to look for | Risk if missing |
|---------|-----------------|-----------------|
| `->method()` on a variable | `isObjectType($node->var, new ObjectType('Fully\Qualified\Interface'))` | Any class with this method name is transformed |
| `->property` on a variable | Same `isObjectType` on `$node->var` | Any class with this property is transformed |
| `$this->method()` inside a class body | `isObjectType($node->var, ...)` or `extends`-check on the enclosing `Class_` node | Any class with this method is transformed, not just the intended subclass |
| `ClassName::method()` static call | `isName($node->class, 'ClassName')` or `isObjectType` | Low risk if the class name is unique, but still worth verifying |
| Global function call `foo()` | None needed | SAFE — function names are global |
| Class *declaration* (`class Foo extends Bar`) | Check `extends` on the `Class_` node before inspecting methods | EXEMPT category — different pattern |

## Steps for Each Rector

1. **Read** the rector source file.
2. **Identify** what node types it matches (look at `getNodeTypes()` and the early-return guards in `refactor()`).
3. **Check the guard**: for every method call, property fetch, or `$this` reference, is there an `isObjectType()` call that constrains the owning class?
4. **Classify**:
   - `SAFE` — correct type guard present, or targets global functions/constants only
   - `AT-RISK` — matches name without a type guard; needs fixing
   - `EXEMPT` — operates on a class declaration and checks the parent class
5. **For AT-RISK rectors**: identify the Drupal class or interface that owns the deprecated member, check whether a stub exists in `stubs/`, add one if needed, then add the `isObjectType` guard and update fixtures.

## Finding the Right Class/Interface

For the `isObjectType` guard you need the FQCN of the interface or class that declares the deprecated member. Look it up in `repos/drupal-core` (run `bash .claude/scripts/setup-repos.sh` if absent):

```bash
grep -rn "function <methodName>\|property \$<propertyName>" repos/drupal-core/core --include="*.php" -l | head -5
```

Prefer the *interface* over the concrete class when one exists — this catches all implementations.

## Stub Pattern

If the class is not already in `stubs/`, create a minimal stub:

```php
<?php
declare(strict_types=1);
namespace Drupal\Some\Namespace;

if (class_exists(\Drupal\Some\Namespace\ClassName::class)) {
    return;
}

class ClassName {}   // or: interface InterfaceName {}
```

Place it at `stubs/Drupal/Some/Namespace/ClassName.php`, then run `composer dump-autoload`.

## Fixture Update Pattern

After adding a type guard, fixtures must provide type context so PHPStan can resolve `isObjectType`:

- For a variable: add `/** @var \Fully\Qualified\Interface $var */` above the call.
- For `$this`: wrap the code in a class that `extends` or `implements` the target type (the stub makes this work without loading all of Drupal).
- Add a `no_change_unrelated.php.inc` fixture showing an untyped or wrong-typed caller is left alone.

## Running on the Branch Checklist

If `$ARGUMENTS` is empty or `all`, open `.claude/skills/prompts/rector-type-specificity-checklist.md` and work through every row marked `AT-RISK`, one by one. After fixing each rector, tick the row in the checklist.

If `$ARGUMENTS` is a class name (e.g. `RemoveConfigSaveTrustedDataArgRector`), locate the file with:

```bash
find src -name "<ClassName>.php"
```

Then apply the five steps above to that single rector only.

## What a Fix Looks Like

```php
// Before — matches any ->save() call:
if (!$this->isName($node->name, 'save')) {
    return null;
}

// After — only matches Config::save():
if (!$this->isName($node->name, 'save')) {
    return null;
}
if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Config\Config'))) {
    return null;
}
```

Always add the `isObjectType` check *after* the name check so PHPStan's heavier type resolution only runs when the name already matches.
