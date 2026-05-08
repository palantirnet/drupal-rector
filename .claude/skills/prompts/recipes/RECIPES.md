# Recipe Index

Recipes are fill-in-the-blank templates that replace the 14-step exploration workflow
in `digest-to-rector-prompt.md` for well-understood patterns.

**How to use:** Identify which recipe matches the digest rule, extract the required values,
and follow only that recipe. Skip `digest-to-rector-prompt.md` entirely.

---

## Routing guide

Read the digest. Answer these questions in order:

1. **What node type is transformed?**
   - `FuncCall` → continue
   - `MethodCall` / `NullsafeMethodCall` → go to **method-rename** or **custom**
   - `ClassConstFetch` → go to **class-const-rename** or **global-const-to-class-const**
   - `String_` → **custom** (no recipe yet)
   - Multiple node types in one class → **custom** (`digest-to-rector-prompt.md`)

2. **What does the replacement look like?**
   - `fn()` is removed entirely, no replacement → **func-removal** (config-only)
   - `SomeClass::staticMethod()` → **func-to-static** (config-only)
   - `\Drupal::service('string.id')->method()` → **func-to-string-service** (config-only)
   - `\Drupal::service(FqcnClass::class)->method()`, one function → **func-to-class-service-bc** ✓
   - `\Drupal::service(FqcnClass::class)->method()`, multiple functions → **func-to-class-service-bc-multi** ✓
   - `$firstArg->method()` (method on first argument) → **func-to-first-arg-method** (config-only)
   - `\Drupal::service('old.id')` → `\Drupal::service('new.id')` → **service-rename** (config-only)
   - Something else → **custom** (`digest-to-rector-prompt.md`)

3. **For MethodCall:** which generic rector applies?
   - Rename with receiver type check → **method-rename** (config-only)
   - Complex transformation → **custom**

4. **For ClassConstFetch:**
   - `OldClass::CONST` → `NewClass::CONST` → **class-const-rename** (config-only)
   - `GLOBAL_CONST` → `SomeClass::CONST` → **global-const-to-class-const** (config-only)

---

## Recipe status

| Recipe file | Pattern | Type | Status |
|---|---|---|---|
| `func-to-class-service-bc.md` | FuncCall → `Fqcn::class` service, single | custom class | ✅ done |
| `func-to-class-service-bc-multi.md` | FuncCall → `Fqcn::class` service, multiple | custom class | ✅ done |
| `func-removal.md` | FuncCall removed entirely | config-only | 🔲 todo |
| `func-to-static.md` | FuncCall → static method | config-only | 🔲 todo |
| `func-to-string-service.md` | FuncCall → `'service.id'` method | config-only | 🔲 todo |
| `func-to-first-arg-method.md` | FuncCall → method on first arg | config-only | 🔲 todo |
| `service-rename.md` | `\Drupal::service('old')` → `'new'` | config-only | 🔲 todo |
| `method-rename.md` | MethodCall rename with type check | config-only | 🔲 todo |
| `class-const-rename.md` | `OldClass::CONST` → `NewClass::CONST` | config-only | 🔲 todo |
| `global-const-to-class-const.md` | `GLOBAL_CONST` → `Class::CONST` | config-only | 🔲 todo |

---

## Notes on config-only recipes

Config-only recipes do **not** create a new PHP class. They:

1. Add one entry to an existing config file (e.g. `config/drupal-11/drupal-11.4-deprecations.php`)
2. Add one fixture file to the existing generic rector's test directory
3. Add the entry to that rector's test config
4. Run the existing test suite for that rector

The config file to edit depends on `introducedVersion` — same lookup table as the
custom-class recipes. If the file does not yet import the generic rector class, add the
`use` statement.

---

## What belongs in a recipe vs. the main prompt

Use a recipe when **all** of these are true:
- The transformation pattern is fully determined (no ambiguity)
- The base class and BC-wrapping decision are obvious from the pattern
- The output is identical boilerplate except for 5–8 substitution values

Use `digest-to-rector-prompt.md` when:
- The digest rule uses multiple node types that need different BC treatment
- The replacement logic is conditional (e.g., depends on surrounding AST context)
- The rule removes or restructures nodes rather than substituting them
- You are unsure which recipe applies
