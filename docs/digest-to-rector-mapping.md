# Pattern Mapping: drupal-digests → drupal-rector

This document maps the structural differences between AI-generated rules from the
[drupal-digests](https://github.com/dbuytaert/drupal-digests) repository and the conventions
expected in drupal-rector. Use this as a reference when converting a digests rule.

---

## 1. Side-by-side comparison

### drupal-digests rule (source)

```php
<?php
// Source: https://www.drupal.org/node/3550054
// (no namespace declaration)

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Identifier;
use Rector\Config\RectorConfig;          // ← only needed by the wrapper config, not the rule
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class FormLocationRector extends AbstractRector
{
    private const MAP = [
        'FORM_BELOW' => 'Below',
        'FORM_SEPARATE_PAGE' => 'SeparatePage',
    ];

    public function getRuleDefinition(): RuleDefinition { ... }
    public function getNodeTypes(): array { ... }
    public function refactor(Node $node): ?Node { ... }
}
```

### drupal-rector equivalent (target)

```php
<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;    // ← proper namespace

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use Rector\Rector\AbstractRector;                      // ← or AbstractDrupalCoreRector
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class FormLocationRector extends AbstractRector         // ← non-final preferred in drupal-rector
{
    private const MAP = [ ... ];                        // ← logic unchanged

    public function getRuleDefinition(): RuleDefinition { ... }
    public function getNodeTypes(): array { ... }
    public function refactor(Node $node): ?Node { ... }
}
```

**Key differences:**

| Aspect | drupal-digests | drupal-rector |
|---|---|---|
| Namespace | none | `DrupalRector\Drupal11\Rector\Deprecation` |
| Class modifier | `final` | none (not final) |
| `declare(strict_types=1)` | sometimes | always |
| `use Rector\Config\RectorConfig` | included in rule file | NOT in rule file (belongs in configs only) |
| Filename | `replace-...-3550054.php` | `FormLocationRector.php` |
| Location | `rector/rules/` in digests repo | `src/Drupal11/Rector/Deprecation/` in drupal-rector |

---

## 2. Base class decision tree

```
Does the rule transform a node type that is CallLike?
  (CallLike = FuncCall, MethodCall, StaticCall, New_)

  YES → Does both the OLD node and the NEW node have CallLike type?
          YES → Was the deprecation introduced in Drupal >= 10.1.0?
                  YES → Use AbstractDrupalCoreRector + DrupalIntroducedVersionConfiguration
                         (BC wrapping fires automatically)
                  NO  → Use AbstractRector (no BC wrapping available for old Drupal versions)
          NO  → Use AbstractRector (e.g. FuncCall input but ClassConstFetch output)

  NO  → Use AbstractRector
         Examples: ClassConstFetch, Class_, return types, property declarations
```

### Quick reference

| Input node | Output node | Introduced | Base class | BC wrapping |
|---|---|---|---|---|
| `FuncCall` | `StaticCall` | >= 10.1.0 | `AbstractDrupalCoreRector` | Yes |
| `FuncCall` | `MethodCall` | >= 10.1.0 | `AbstractDrupalCoreRector` | Yes |
| `FuncCall` | `StaticCall` | < 10.1.0 | `AbstractRector` | No |
| `ClassConstFetch` | `ClassConstFetch` | any | `AbstractRector` | No |
| `New_` (arg modification) | `New_` | any | `AbstractRector` | No |
| `Class_` (structural) | `Class_` | any | `AbstractRector` | No |

---

## 3. Generic/configurable rectors — check these before writing a custom class

drupal-rector ships several data-driven rectors in `src/Rector/Deprecation/` that handle common
deprecation patterns with zero custom PHP. **Always check whether one of these covers the
transformation before writing a new class.**

### Available generic rectors

#### `FunctionToStaticRector` — deprecated global function → static class call

Config object: `FunctionToStaticConfiguration(introducedVersion, deprecatedFunctionName, className, methodName, [argReorder])`

- Transforms `some_function($a, $b)` → `\ClassName::methodName($a, $b)`
- Uses `AbstractDrupalCoreRector` → BC-wrapped automatically for `introducedVersion >= 10.1.0`
- Optional `argReorder` map swaps argument positions: `[0 => 1, 1 => 0]` reverses two args

```php
new FunctionToStaticConfiguration('11.4.0', 'language_configuration_element_submit', 'Drupal\language\Element\LanguageConfiguration', 'submit'),
```

Fixture output (BC-wrapped):
```php
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\language\Element\LanguageConfiguration::submit($form, $form_state), fn() => language_configuration_element_submit($form, $form_state));
```

#### `FunctionToServiceRector` — deprecated global function → service method call

Config object: `FunctionToServiceConfiguration(introducedVersion, deprecatedFunctionName, serviceName, serviceMethodName)`

- Transforms `some_function($a)` → `\Drupal::service('service.name')->method($a)`
- Uses `AbstractDrupalCoreRector` → BC-wrapped automatically for `introducedVersion >= 10.1.0`
- **`serviceName` is a string literal** (e.g., `'theme.registry'`), not a class constant
- If the service is a class-based service (e.g., `\Drupal\language\Hook\LanguageHooks`), pass the FQCN as a string

```php
new FunctionToServiceConfiguration('11.4.0', 'language_process_language_select', 'Drupal\language\Hook\LanguageHooks', 'processLanguageSelect'),
```

Fixture output (BC-wrapped):
```php
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\language\Hook\LanguageHooks')->processLanguageSelect($element), fn() => language_process_language_select($element));
```

#### `ClassConstantToClassConstantRector` — deprecated class constant → new class constant

Config object: `ClassConstantToClassConstantConfiguration(deprecatedClass, deprecatedConstant, newClass, newConstant)`

- Transforms `OldClass::OLD_CONST` → `\NewClass::NewConst`
- Uses `AbstractRector` → **no BC wrapping** (ClassConstFetch is not a CallLike node)
- No `introducedVersion` parameter — applies unconditionally

```php
new ClassConstantToClassConstantConfiguration('Drupal\comment\Plugin\Field\FieldType\CommentItemInterface', 'FORM_BELOW', 'Drupal\comment\FormLocation', 'Below'),
```

Fixture output (no BC wrapping):
```php
$location = \Drupal\comment\FormLocation::Below;
```

### Where to add generic rector configurations

New configurations for existing generic rectors go into the appropriate versioned config file under `config/drupal-11/`:

```php
// config/drupal-11/drupal-11.4-deprecations.php
$rectorConfig->ruleWithConfiguration(ClassConstantToClassConstantRector::class, [
    new ClassConstantToClassConstantConfiguration(...),
]);
$rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
    new FunctionToStaticConfiguration('11.4.0', ...),
]);
$rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
    new FunctionToServiceConfiguration('11.4.0', ...),
]);
```

Test coverage for generic rectors lives in `tests/src/Rector/Deprecation/[RectorName]/` — add a
new fixture file and a config entry there rather than creating a new test directory.

### Decision: generic rector vs custom class

| Pattern | Generic rector available? |
|---|---|
| Global function → static class method | `FunctionToStaticRector` ✓ |
| Global function → `\Drupal::service(…)->method()` | `FunctionToServiceRector` ✓ |
| Class constant → different class constant | `ClassConstantToClassConstantRector` ✓ |
| Function with complex arg transformation | Custom class |
| Constructor arg injection / `__construct` changes | Custom class |
| Multiple node types with different base classes | Custom classes (split by type) |
| Return type / property declaration changes | Custom class |

---

## 4. BC wrapping — how it works

When using `AbstractDrupalCoreRector`, the base class `refactor()` method automatically wraps
`CallLike → CallLike` transformations in `DeprecationHelper::backwardsCompatibleCall()`, allowing
the generated code to run on both old and new Drupal versions simultaneously.

### What you write

```php
// Your rule class extends AbstractDrupalCoreRector
// You implement refactorWithConfiguration() instead of refactor()

public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration)
{
    if (!$node instanceof Node\Expr\FuncCall || $this->getName($node) !== 'some_deprecated_function') {
        return null;
    }
    // Return the NEW call — the base class wraps it automatically
    return $this->nodeFactory->createStaticCall('SomeClass', 'newMethod', $node->getArgs());
}
```

### What Rector emits in the target code

```php
// Before (in the fixture)
some_deprecated_function($arg);

// After (with BC wrapping, for >= 10.1.0 deprecations)
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(
    \Drupal::VERSION,
    '11.1.0',
    static fn() => \SomeClass::newMethod($arg),
    static fn() => some_deprecated_function($arg)
);
```

### AbstractDrupalCoreRector rule structure

```php
class MyDeprecationRector extends AbstractDrupalCoreRector
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

    public function getNodeTypes(): array
    {
        return [Node\Expr\FuncCall::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration)
    {
        if (!$node instanceof Node\Expr\FuncCall || $this->getName($node) !== 'the_old_function') {
            return null;
        }
        // Return the replacement node — base class adds BC wrapping
        return $this->nodeFactory->createStaticCall('NewClass', 'newMethod', $node->getArgs());
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Description of what this fixes', [
            new ConfiguredCodeSample(
                'the_old_function($arg);',
                '\NewClass::newMethod($arg);',
                [new DrupalIntroducedVersionConfiguration('11.1.0')]
            ),
        ]);
    }
}
```

---

## 5. Fixture file format

Test fixtures live at:
```
tests/src/Drupal11/Rector/Deprecation/[RuleName]/fixture/basic.php.inc
```

### Format

```
<?php
[PHP code before transformation — copied from CodeSample first argument]
?>
-----
<?php
[PHP code after transformation — copied from CodeSample second argument]
?>
```

### Worked example (FormLocationRector)

```
<?php

use Drupal\comment\Plugin\Field\FieldType\CommentItemInterface;

$location = CommentItemInterface::FORM_BELOW;
$other = CommentItemInterface::FORM_SEPARATE_PAGE;
?>
-----
<?php

use Drupal\comment\Plugin\Field\FieldType\CommentItemInterface;

$location = \Drupal\comment\FormLocation::Below;
$other = \Drupal\comment\FormLocation::SeparatePage;
?>
```

**Notes:**
- The `-----` separator must be on its own line with no trailing spaces.
- Use statements that are no longer needed after transformation should be removed in the "after" section.
- The `<?php` and `?>` tags are required (this is how `AbstractRectorTestCase` parses the sections).
- For BC-wrapped rules, the "after" section shows the `DeprecationHelper::backwardsCompatibleCall()` call.

---

## 6. Namespace and file placement

All Drupal 11 deprecation rules go into:
- **Rule class:** `src/Drupal11/Rector/Deprecation/[RuleName]Rector.php`
- **Namespace:** `DrupalRector\Drupal11\Rector\Deprecation`

### Class name derivation from digests filename

The drupal-digests filename format is:
```
[action-verb]-[description]-[issue-number].php
```

Steps to derive the class name:
1. Strip the issue number suffix (e.g., `-3550054`).
2. Convert the remaining kebab-case to PascalCase: `replace-deprecated-commentiteminterface-form-below-and-form` → `ReplaceDeprecatedCommentiteminterfaceFormBelowAndForm`
3. Check if the digests rule already has a simpler class name (it often does — the class name inside the file is more descriptive than the filename). **Always use the class name from the file, not the filename.**
4. Append `Rector` if not already present.

**Examples:**
- File: `replace-deprecated-commentiteminterface-form-below-and-form-3550054.php`
- Class inside file: `FormLocationRector`
- drupal-rector class: `FormLocationRector` ✓ (already has Rector suffix)

- File: `add-componentpluginmanager-to-themeinstaller-constructor-3522505.php`
- Class inside file: `AddComponentPluginManagerToThemeInstallerRector`
- drupal-rector class: `AddComponentPluginManagerToThemeInstallerRector` ✓

### Version mapping

All drupal-digests rules currently target Drupal 11.x deprecations. The issue markdown
(`issues/drupal-core/[issue-number].md`) states the exact version under the `## Impact` section:
```
- **Deprecation:** ... deprecated in drupal:11.4.0, removed in drupal:13.0.0 ...
```

Use this to:
- Confirm the namespace (`Drupal11`).
- Extract the `introducedVersion` string for `DrupalIntroducedVersionConfiguration` (e.g., `'11.4.0'`).

---

## 7. Test config patterns

### Simple rule (no configuration, uses AbstractRector)

```php
// tests/src/Drupal11/Rector/Deprecation/FormLocationRector/config/configured_rule.php
<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\FormLocationRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(FormLocationRector::class, $rectorConfig, false);
};
```

`DeprecationBase::addClass($class, $rectorConfig, $addNoticeConfig, $configuration)`:
- `$addNoticeConfig` — set `true` when the rule uses `AddCommentService` to insert review notices.
- `$configuration` — array of configuration objects (e.g., `[new DrupalIntroducedVersionConfiguration('11.4.0')]`).

### BC-capable rule (uses AbstractDrupalCoreRector)

```php
// tests/src/Drupal11/Rector/Deprecation/MyFunctionRector/config/configured_rule.php
<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\MyFunctionRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(MyFunctionRector::class, $rectorConfig, false, [
        new DrupalIntroducedVersionConfiguration('11.1.0'),
    ]);
};
```

---

## 8. Test class template

```php
// tests/src/Drupal11/Rector/Deprecation/[RuleName]/[RuleName]Test.php
<?php

declare(strict_types=1);

namespace Drupal11\Rector\Deprecation\[RuleName];

use Rector\Testing\PHPUnit\AbstractRectorTestCase;

class [RuleName]Test extends AbstractRectorTestCase
{
    /**
     * @covers ::refactor
     * @dataProvider provideData
     */
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

---

## 9. Multi-node-type rules

Some drupal-digests rules handle two or more distinct node types in a single class
(e.g., both `ClassConstFetch` and `FuncCall`). When converting:

- **If both transformations are simple (no BC):** Keep them in one class, use `AbstractRector`.
  Both node types are listed in `getNodeTypes()` and handled by type-checking inside `refactor()`.
- **If one transformation needs BC and the other doesn't:** Split into two separate rector classes.
  This is necessary because `AbstractDrupalCoreRector::refactor()` assumes all transformations
  use the same BC configuration.

---

## 10. AddCommentService

Some drupal-rector rules add a comment to the output code to prompt human review. When to use it:

- The transformation is not fully automatic (e.g., requires developer judgment to complete).
- The digests rule itself contains a `// TODO` or review note in the transformed code.
- The output might need manual adjustment depending on context.

When NOT to use it (the common case):
- The transformation is deterministic (one-to-one constant/function replacement).

If used:
```php
// In the rule constructor
public function __construct(private readonly AddCommentService $commentService) {}

// In refactor()
$this->commentService->addDrupalRectorComment($node, 'Please verify this change manually.');
```

And in the test config:
```php
DeprecationBase::addClass(MyRector::class, $rectorConfig, true);  // true = addNoticeConfig
```

---

## 11. Test environment: Drupal VERSION stub

The stub at `stubs/Drupal/Drupal.php` provides the `\Drupal::VERSION` constant used by
`AbstractDrupalCoreRector::rectorShouldApplyToDrupalVersion()`. The stub must be set to a version
**at least as high as the highest introduced version** among the rules being tested.

| Scenario | Required stub VERSION |
|---|---|
| Only Drupal 8/9/10 rules | `10.99.x-dev` (original default) |
| Drupal 11.x rules | `11.99.x-dev` |

The stub has been updated to `11.99.x-dev`. This value is safe for all existing Drupal 8/9/10
tests — the BC logic only requires `installedVersion >= 10.1.0`, which `11.99.0` satisfies.

**Do not change the stub back to `10.99.x-dev`** — doing so will silently disable all Drupal11
rules in the test suite (their `refactor()` won't fire, tests pass trivially).
