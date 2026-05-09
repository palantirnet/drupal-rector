# Conversion Prompt: drupal-digests → drupal-rector

This is a structured prompt for converting a single
[drupal-digests](https://github.com/dbuytaert/drupal-digests) rector rule into a
fully drupal-rector–compliant implementation with tests.

**Usage:** Start a Claude Code session in the drupal-rector repository, then say:

> Convert the drupal-digests rule at `[path-to-rule-file]` following the prompt in
> `.claude/skills/prompts/digest-to-rector-prompt.md`.

The agent will read both this document and the target rule, then produce all output files.

---

## Prerequisites

The following directories must exist (created by the scaffold in U1):
- `src/Drupal11/Rector/Deprecation/`
- `tests/src/Drupal11/Rector/Deprecation/`

If they are missing, run: `mkdir -p src/Drupal11/Rector/Deprecation tests/src/Drupal11/Rector/Deprecation`

**Stub version:** The test stub at `stubs/Drupal/Drupal.php` has `VERSION = '11.99.x-dev'`. This
is the default version used by `AbstractDrupalCoreRector::installedDrupalVersion()` for any test
that does not set an explicit override. Do not revert it to `10.99.x-dev` — that would silently
disable all Drupal 11 rules in the test suite.

For tests that need to simulate a specific Drupal version (e.g., to verify a rule does NOT fire
on an older version), use `DrupalRectorSettings::setDrupalVersion($version)` via the service
container, and reset it in a `finally` block. Standard conversion tests do not need this — the
stub default (`11.99.x-dev`) is sufficient for normal fixture testing.

---

## Step 1 — Confirm input

You will be given a path to a drupal-digests rule file. Confirm the file exists and read it
completely. The file is typically at:
```
[path-to-drupal-digests-repo]/rector/rules/[rule-filename].php
```

Extract from the file:
- **Class name** — the PHP class name (e.g., `FormLocationRector`)
- **Node types** — the array returned by `getNodeTypes()` (e.g., `[ClassConstFetch::class]`)
- **Refactor logic** — the full body of `refactor()` (or `refactorWithConfiguration()` if present)
- **CodeSample before** — the first string argument to `CodeSample` or `ConfiguredCodeSample`
- **CodeSample after** — the second string argument to `CodeSample` or `ConfiguredCodeSample`
- **Issue number** — the number from the filename or from the comment `// Source: https://www.drupal.org/node/[number]`

---

## Step 1b — Split check

If `refactor()` (or the rule's code samples) handles **more than one** deprecated name (function, method, or constant), pause before proceeding and ask:

> For each deprecated name, could it be independently applied without the others?

**Split them if:**
- Each has a different replacement pattern (e.g., one → string literal, another → service call)
- Each could be useful without the others
- Any individual one fits a generic rector from Step 4b — that one becomes a config entry, not a class method

**Keep them together if:**
- They are semantically inseparable (always migrated as a unit, e.g., an old getter/setter pair)
- They share exactly the same replacement pattern (e.g., 10 procedural functions all mapping to service methods on the same class)

**If splitting:** implement each piece separately — custom rector for patterns that need custom code, config entry for patterns that fit a generic rector — and use distinct, descriptive class names (e.g., `ReplaceTwigExtensionRector` not `TwigEngineFunctionsRector`).

---

## Step 2 — Read the companion issue markdown

The issue markdown is at:
```
[path-to-drupal-digests-repo]/issues/drupal-core/[issue-number].md
```

Read it completely. Extract:
- **Introduced version** — from the `## Impact` section, e.g.:
  `deprecated in drupal:11.4.0` → `'11.4.0'`
- **Removal version** — e.g., `removed in drupal:13.0.0` → `'13.0.0'`
- **New API FQCN** — the fully-qualified class name of the replacement API, from `## Upgrade` or `## Technical details`
- **Description** — one-sentence summary of what this rule does
- **Change record number** — scan for any `drupal.org/node/` link in the "Upgrade path",
  "Change record", or "Technical details" sections. A link like
  `[#3567879](https://www.drupal.org/node/3567879)` is the change record node number.
  Note it separately from the issue number — they are usually different.

If any of the above (including the change record number) are missing or ambiguous, proceed to Step 3. Otherwise skip Step 3.

---

## Step 3 — Optional: fetch from Drupal.org (only if Step 2 was insufficient)

If the introduced version, removal version, replacement FQCN, **or change record number** is not clear from the markdown:

Fetch the Drupal.org issue page:
```
https://www.drupal.org/node/[issue-number]
```

Look for:
- A "Change records for this issue" section or "Related change records" block — the linked node number is the change record.
- The `deprecated in drupal:X.Y.Z` wording and code examples for version/FQCN confirmation.

---

## Step 4 — Classify the rule (BC decision)

Answer these questions using the information gathered:

**Q1: What node types does the rule process?**
- List each type from `getNodeTypes()`.

**Q2: Is there an Expr → Expr transformation?**
- The authoritative check (from `AbstractDrupalCoreRector::refactor()` line 92) is:
  `if ($node instanceof Node\Expr && $result instanceof Node\Expr)`.
- If **both** the input node and the returned node are `Node\Expr` subtypes → BC wrapping is **eligible**.
- `Node\Expr` subtypes include: `FuncCall`, `MethodCall`, `StaticCall`, `NullsafeMethodCall`,
  `New_`, `Array_`, `ClassConstFetch`, `ConstFetch`, `String_`, `Int_`, `PropertyFetch`, and more.
- `Class_` (structural node) and `ArrayItem` are **not** `Node\Expr` → BC wrapping is not applicable.
- Exception: `ArrayItem` cannot appear as an arrow function body in PHP syntax, so even though
  `Node\Expr\ArrayItem` exists, it cannot be BC-wrapped. If a rector handles `ArrayItem` nodes
  alongside BC-wrappable nodes, override `refactor()` to apply the ArrayItem transform directly
  while letting the parent handle BC for other node types (see edge case note in Template B).

**Q3: Was the deprecation introduced in Drupal >= 10.1.0?**
- Compare the introduced version from Step 2 against `10.1.0`.
- If introduced version >= `10.1.0` AND Q2 is eligible → BC wrapping **applies**.
- Otherwise → BC wrapping does **not** apply.

**Decision:**
- BC wrapping applies → Use `AbstractDrupalCoreRector` + `DrupalIntroducedVersionConfiguration`
- BC wrapping does not apply → Use `AbstractRector`

**Quick reference:**

| Input node | Output node | Introduced | Base class | BC wrapping |
|---|---|---|---|---|
| `FuncCall` | `StaticCall` | >= 10.1.0 | `AbstractDrupalCoreRector` | Yes |
| `FuncCall` | `MethodCall` | >= 10.1.0 | `AbstractDrupalCoreRector` | Yes |
| `MethodCall` | `MethodCall` | >= 10.1.0 | `AbstractDrupalCoreRector` | Yes |
| `Array_` | `Array_` | >= 10.1.0 | `AbstractDrupalCoreRector` | Yes |
| `New_` | `New_` | >= 10.1.0 | `AbstractDrupalCoreRector` | Yes |
| `ClassConstFetch` | `ClassConstFetch` | >= 10.1.0 | `AbstractDrupalCoreRector` | Yes |
| `FuncCall` | `StaticCall` | < 10.1.0 | `AbstractRector` | No |
| `ArrayItem` | `ArrayItem` | any | `AbstractRector` | No (PHP syntax limit) |
| `Class_` (structural) | `Class_` | any | `AbstractRector` | No (not an Expr) |

---

## Step 4b — Check for existing generic rectors (BEFORE writing a custom class)

Before generating a new PHP class, check whether the transformation can be expressed as a
configuration entry for an existing generic rector in `src/Rector/Deprecation/`. This is the
preferred path — it avoids creating new classes for patterns that drupal-rector already handles.

**Check the decision table:**

| Transformation pattern | Generic rector to use |
|---|---|
| Global function call removed entirely (no replacement) | `FunctionCallRemovalRector` |
| Global function → static class method | `FunctionToStaticRector` |
| Global function → `\Drupal::service('…')->method()` | `FunctionToServiceRector` |
| Global function → method on its first argument (e.g. `fn($obj)` → `$obj->method()`) | `FunctionToFirstArgMethodRector` |
| `\Drupal::service('old.id')` → `\Drupal::service('new.id')` | `DrupalServiceRenameRector` |
| Instance method renamed (with receiver type check) | `MethodToMethodWithCheckRector` |
| Class constant → different class constant | `ClassConstantToClassConstantRector` |
| Global constant → class constant | `ConstantToClassConstantRector` |
| Class/interface/trait renamed or moved to new namespace | `RenameClassRector` (from Rector core) |
| `DeprecationHelper::backwardsCompatibleCall()` wrapper removal | `DeprecationHelperRemoveRector` |
| Anything else | Write a custom class (continue to Step 5) |

**If a generic rector matches, do this instead of Steps 5–10:**

1. Add the configuration entry to `config/drupal-11/drupal-11.4-deprecations.php` (or the
   appropriate versioned file), inside the matching `$rectorConfig->ruleWithConfiguration()` block.

2. Add a fixture file to the existing generic rector's test directory:
   `tests/src/Rector/Deprecation/[GenericRectorName]/fixture/[descriptive-name].php.inc`

3. Add the configuration entry to the generic rector's test config:
   `tests/src/Rector/Deprecation/[GenericRectorName]/config/configured_rule.php`

4. Run the existing test suite:
   ```bash
   vendor/bin/phpunit tests/src/Rector/Deprecation/[GenericRectorName]/
   ```

5. Skip to Step 11 (fix-style) then Step 12 (phpstan) then Step 13 (test) then Step 14 (commit).

**Configuration entry syntax by generic rector:**

```php
// FunctionCallRemovalRector — removes the entire statement; no replacement
new FunctionCallRemovalConfiguration('[deprecatedFunctionName]'),

// FunctionToStaticRector
new FunctionToStaticConfiguration('[introducedVersion]', '[deprecatedFunctionName]', '[ClassName]', '[methodName]'),
// optional 5th arg: arg reorder map, e.g. [0 => 1, 1 => 0] to swap first two args

// FunctionToServiceRector
new FunctionToServiceConfiguration('[introducedVersion]', '[deprecatedFunctionName]', '[ServiceName]', '[serviceMethodName]'),
// ServiceName is a string literal: 'theme.registry' or 'Drupal\module\Hook\SomeHooks'

// MethodToMethodWithCheckRector — receiver must be typed as the given interface/class
new MethodToMethodWithCheckConfiguration('[ReceiverClass\\FQCN]', '[oldMethodName]', '[newMethodName]'),
// no introducedVersion — applies unconditionally; no BC wrapping

// ClassConstantToClassConstantRector
new ClassConstantToClassConstantConfiguration('[OldClass\\FQCN]', '[OLD_CONST]', '[NewClass\\FQCN]', '[NewConst]'),
// no introducedVersion — applies unconditionally; no BC wrapping

// ConstantToClassConstantRector — replaces bare global constant (ConstFetch) with class constant
new ConstantToClassConfiguration('[GLOBAL_CONSTANT_NAME]', '[TargetClass\\FQCN]', '[CONST_NAME]'),
// no introducedVersion — applies unconditionally; no BC wrapping

// FunctionToFirstArgMethodRector — fn($obj) → $obj->method(); first arg must be the receiver
new FunctionToFirstArgMethodConfiguration('[introducedVersion]', '[deprecatedFunctionName]', '[methodName]'),
// introducedVersion triggers DeprecationHelper BC wrapping; omit (use D9 BC wrapper) for older entries

// DrupalServiceRenameRector — \Drupal::service('old.id') → \Drupal::service('new.id')
new DrupalServiceRenameConfiguration('[introducedVersion]', '[deprecated.service.id]', '[new.service.id]'),
// introducedVersion triggers DeprecationHelper BC wrapping; omit (use D8 BC wrapper) for older entries

// RenameClassRector — pass an associative array directly, not a configuration object
$rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
    '[Old\\Class\\FQCN]' => '[New\\Class\\FQCN]',
]);
// use Rector\Renaming\Rector\Name\RenameClassRector; at top of config file
```

**If no generic rector matches, continue to Step 5 to generate a custom class.**

---

## Step 5 — Derive the class name and file paths

**Class name:**
- Use the class name from the digests rule file (not the filename).
- The class name typically already ends in `Rector`. If not, append it.

**drupal-rector file paths (all relative to the drupal-rector repo root):**
```
src/Drupal11/Rector/Deprecation/[ClassName].php
tests/src/Drupal11/Rector/Deprecation/[ClassName]/[ClassName]Test.php
tests/src/Drupal11/Rector/Deprecation/[ClassName]/config/configured_rule.php
tests/src/Drupal11/Rector/Deprecation/[ClassName]/fixture/basic.php.inc
```

---

## Step 6 — Generate the rule class

Write `src/Drupal11/Rector/Deprecation/[ClassName].php`.

### Template A: Simple rule (AbstractRector, no BC)

Use when Step 4 concluded: BC wrapping does NOT apply.

```php
<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
// [copy only the use statements actually needed by the refactor logic — omit Rector\Config\RectorConfig]
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * [description from Step 2]
 *
 * @see https://www.drupal.org/node/[issue-number]
 * @see https://www.drupal.org/node/[change-record-number]
 */
class [ClassName] extends AbstractRector
{
    // [copy private constants and properties from the digests rule unchanged]

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            '[description from Step 2]',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
[CodeSample before string from Step 1]
CODE_BEFORE,
                    <<<'CODE_AFTER'
[CodeSample after string from Step 1]
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [/* [node types from Step 1] */];
    }

    /** @param [NodeType] $node */
    public function refactor(Node $node): ?Node
    {
        // [copy refactor() body from the digests rule unchanged]
    }
}
```

### Template B: BC-capable rule (AbstractDrupalCoreRector)

Use when Step 4 concluded: BC wrapping APPLIES.

```php
<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
// [copy only the use statements actually needed by the refactor logic]
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * [description from Step 2]
 *
 * @see https://www.drupal.org/node/[issue-number]
 * @see https://www.drupal.org/node/[change-record-number]
 */
class [ClassName] extends AbstractDrupalCoreRector
{
    /** @var DrupalIntroducedVersionConfiguration[] */
    protected array $configuration;

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalIntroducedVersionConfiguration) {
                throw new \InvalidArgumentException(sprintf(
                    'Each configuration item must be an instance of "%s"',
                    DrupalIntroducedVersionConfiguration::class
                ));
            }
        }
        parent::configure($configuration);
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [/* [node types from Step 1] */];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration)
    {
        // [copy refactor() body from the digests rule — the base class handles BC wrapping automatically]
        // Important: return the NEW call node. Do NOT call parent::refactor() or handle BC here.
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            '[description from Step 2]',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
[CodeSample before string from Step 1]
CODE_BEFORE,
                    <<<'CODE_AFTER'
[CodeSample after string from Step 1]
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('[introduced version from Step 2]')]
                ),
            ]
        );
    }
}
```

**Adaptation notes:**
- Remove `final` keyword — drupal-rector classes are not final.
- Remove `use Rector\Config\RectorConfig` from the rule class (it belongs only in config files).
- Keep all private constants, arrays, and helper methods unchanged.
- For multi-node-type rules (two or more different node types in `getNodeTypes()`):
  - **If both transformations are simple (no BC):** Keep them in one class, use `AbstractRector`.
    Both node types go in `getNodeTypes()` and are handled by type-checking inside `refactor()`.
  - **If one needs BC and the other doesn't:** Split into two separate rector classes.
    `AbstractDrupalCoreRector::refactor()` assumes all transformations share the same BC
    configuration, so mixing is not possible in a single class.

**Shallow-clone warning (critical for correctness):**
PHP's `clone` is a shallow copy — child objects in arrays (e.g. `$node->args[]`, `$node->items[]`)
are the same object instances in both the original and the clone. If you mutate a child after
cloning the parent, the mutation appears on **both** the original and the cloned node. This breaks
BC wrapping: both the `fn() => <new>` and `fn() => <old>` sides of the
`DeprecationHelper::backwardsCompatibleCall()` will show the mutated (new) value.

**Rule:** Always clone child nodes before mutating them:
```php
// WRONG — mutates the shared Arg object; BC call gets new value on both sides
$arg->value = $replacement;

// CORRECT — clone the child, mutate the clone, put it back in the cloned parent
$newArg = clone $arg;
$newArg->value = $replacement;
$cloned->args[$index] = $newArg;
```
This applies to any child node you modify: `Arg`, `ArrayItem`, `Node\Identifier`, etc.

**ArrayItem edge case:**
`ArrayItem` (`Node\Expr\ArrayItem`) cannot appear as an arrow function body in valid PHP. If a
rector handles `ArrayItem` nodes alongside BC-wrappable nodes (e.g. `MethodCall`), override
`refactor()` to apply ArrayItem transforms directly and skip the BC path for that node type:
```php
public function refactor(Node $node): ?Node
{
    if ($node instanceof ArrayItem) {
        // Apply directly; BC wrapping is not applicable for ArrayItem.
        foreach ($this->configuration as $configuration) {
            if (!$this->rectorShouldApplyToDrupalVersion($configuration)) {
                continue;
            }
            if ($this->isInBackwardsCompatibleCall($node)) {
                continue;
            }
            return $this->refactorArrayItem($node);
        }
        return null;
    }
    // Let the parent handle BC wrapping for all other Expr nodes.
    return parent::refactor($node);
}
```

---

## Step 7 — Generate the fixture file

Write `tests/src/Drupal11/Rector/Deprecation/[ClassName]/fixture/basic.php.inc`.

Format:
```
<?php

[before code — from CodeSample first argument, as valid PHP]
?>
-----
<?php

[after code — from CodeSample second argument, as valid PHP]
?>
```

**Rules:**
- The `-----` separator must be on its own line with no surrounding whitespace.
- Remove `use` statements from the "after" section if the new code uses FQCNs (backslash-prefixed).
- For BC-wrapped rules: the "after" section must show the full
  `\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, 'X.Y.Z', fn() => <new>, fn() => <old>)`
  output rather than the plain transformed code. The exact format is produced by the rector at
  runtime — if unsure, run `vendor/bin/phpunit` once and read the failure diff to copy the actual output.
- If the CodeSample before/after strings are not full PHP files, wrap them appropriately (add `<?php\n\n` prefix and `\n` suffix before the `?>`).
- Add realistic surrounding context if the snippet is very minimal (e.g., wrap a bare expression in a function body).

---

## Step 8 — Generate the test class

Write `tests/src/Drupal11/Rector/Deprecation/[ClassName]/[ClassName]Test.php`.

**For simple rules (AbstractRector, no BC):**

```php
<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal11\Rector\Deprecation\[ClassName];

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class [ClassName]Test extends AbstractRectorTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideData')]
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }
}
```

**For BC-capable rules (AbstractDrupalCoreRector):** Use the full `testAboveVersion` /
`testBelowVersion` form from the QG-B section of SKILL.md. Do NOT use the simple `test()` form
above — the version-gating tests are required for all BC-wrapped rectors.

---

## Step 9 — Generate the test config

Write `tests/src/Drupal11/Rector/Deprecation/[ClassName]/config/configured_rule.php`.

### For simple rules (AbstractRector, no BC)

```php
<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\[ClassName];
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass([ClassName]::class, $rectorConfig, false);
};
```

### For BC-capable rules (AbstractDrupalCoreRector)

```php
<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\[ClassName];
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass([ClassName]::class, $rectorConfig, false, [
        new DrupalIntroducedVersionConfiguration('[introduced version from Step 2]'),
    ]);
};
```

**Note:** Set the third argument of `DeprecationBase::addClass()` to `true` only if the rule uses
`AddCommentService` to insert human-review notices. This is uncommon — most rules use `false`.

**When to use `AddCommentService`:** Only when the transformation is not fully automatic and
requires developer judgment (e.g., the digests rule contains a `// TODO` comment in the output,
or the replacement differs depending on context). Deterministic one-to-one replacements never
need it.

If used, inject it in the constructor and call it in `refactor()`/`refactorWithConfiguration()`:

```php
public function __construct(private readonly AddCommentService $commentService) {}

// Inside refactor():
$this->commentService->addDrupalRectorComment($node, 'Please verify this change manually.');
```

---

## Step 10 — Write all files

Using the write tool, create all four files at the paths derived in Step 5:
1. `src/Drupal11/Rector/Deprecation/[ClassName].php`
2. `tests/src/Drupal11/Rector/Deprecation/[ClassName]/[ClassName]Test.php`
3. `tests/src/Drupal11/Rector/Deprecation/[ClassName]/config/configured_rule.php`
4. `tests/src/Drupal11/Rector/Deprecation/[ClassName]/fixture/basic.php.inc`

---

## Step 11 — Fix code style

Run code style fixer on the generated rule class:

```bash
ddev composer fix-style
```

This normalises import ordering, spacing, and other formatting conventions.

---

## Step 12 — Run static analysis

```bash
ddev composer phpstan
```

Fix any reported issues before proceeding. Common issues:
- Missing `@param` / `@return` types on overridden methods
- Incorrect type hints (e.g., `Node` vs a specific subtype)
- `refactorWithConfiguration()` must declare its return type as mixed or `Node|Node[]|null`

---

## Step 13 — Run the test

```bash
vendor/bin/phpunit tests/src/Drupal11/Rector/Deprecation/[ClassName]/
```

**If tests pass:** The conversion is complete. Commit all four files together.

**If tests fail:** Diagnose the failure:

- **"Expected output does not match actual output"** — The fixture "after" section is wrong.
  Run rector on the "before" section manually to see what it actually produces, then update the fixture.
- **"Class not found"** — Check the namespace declaration and file path match.
- **"Method not found"** — Verify the base class was chosen correctly (Step 4).
- **"Fixture has no before/after separator"** — The `-----` line is missing or has extra spaces.
- **phpstan errors** — Fix type declarations in the rule class and re-run.

After fixing failures, update the conversion prompt (this file) with any decision rule that
prevented the failure from occurring, so future conversions avoid the same issue.

---

## Step 14 — Commit

```bash
git add \
  src/Drupal11/Rector/Deprecation/[ClassName].php \
  tests/src/Drupal11/Rector/Deprecation/[ClassName]/
git commit -m "feat(Drupal11): Add [ClassName] for issue #[issue-number]"
```

Do not push — leave pushing to the human reviewer.

---

## Checklist

Before marking a conversion complete, verify:

- [ ] Step 4b was checked — a generic rector was used if the pattern matched, custom class only if it didn't
- [ ] (Custom class only) `declare(strict_types=1)` is present in the rule class
- [ ] (Custom class only) Namespace is `DrupalRector\Drupal11\Rector\Deprecation`
- [ ] (Custom class only) `final` keyword is removed
- [ ] (Custom class only) `use Rector\Config\RectorConfig` is NOT in the rule class
- [ ] (Custom class only) Base class matches the BC decision from Step 4
- [ ] (Custom class only) `getNodeTypes()` lists all node types from the original rule
- [ ] Fixture `-----` separator is on its own line
- [ ] `vendor/bin/phpunit` passes for the relevant test directory
- [ ] `ddev composer fix-style` has been run
- [ ] `ddev composer phpstan` reports no errors for new/modified files
