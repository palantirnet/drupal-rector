# PHPUnit annotations → attributes Rectors (Drupal 12 readiness)

**Status:** Approved design — ready for implementation planning
**Date:** 2026-06-20
**Branch:** `feat/phpunit-annotations-to-attributes-3552124`
**Related:**
- Drupal core meta [#3527936 Introduce support for PHPUnit 12](https://www.drupal.org/project/drupal/issues/3527936)
- Core rule source [#3446380 Define a Rector rule to convert test annotations to attributes](https://www.drupal.org/project/drupal/issues/3446380) (MR !12218)
- drupal-rector adoption issue [#3552124](https://git.drupalcode.org/project/rector/-/work_items/3552124)
- Investigation: `docs/d11-phpunit-investigation/README.md`

## Problem / motivation

Drupal 12 will require **PHPUnit 12**, which removes annotation-based test metadata
(`@group`, `@dataProvider`, …) in favor of PHP attributes (`#[Group]`, `#[DataProvider]`, …).
Contrib maintainers face the same wall as core, but — unlike core — must keep tests working
on **older** Drupal/PHPUnit at the same time. Adding an attribute *without* removing the
annotation is safe across PHPUnit 9/10/11/12 (unknown attribute classes are ignored at
runtime; confirmed in the Slack thread by benjifisher on D10.1/PHP 8.3 and main/PHP 8.5).

This work adds Rector rules so the Project Update Bot and maintainers can automate the
conversion in a backward-compatible way.

## Scope

**In scope (this iteration):**
- `@group` → `#[Group]` (special case: `@group legacy` → `#[IgnoreDeprecations]`)
- `@dataProvider` → `#[DataProvider]`
- `@depends` → `#[Depends]`
- `@testWith` → `#[TestWith]`
- Add `#[RunTestsInSeparateProcesses]` to all non-Unit test classes

**Out of scope (deferred follow-up):**
- `@covers` / `@coversDefaultClass` / `@coversNothing` / `@uses` — no clean 1:1 mapping
  (`CoversClass` vs `CoversMethod` split, `@covers → @legacy-covers` two-step in core).
- `@medium`/`@small`/`@large`, `@preserveGlobalState`, `@requires`, `@runInSeparateProcess`,
  `@backupGlobals`, etc. — lower frequency; can extend Rector A's config later.

## Architecture

Everything lives in the **PHPUnit space** (`DrupalRector\Rector\PHPUnit`), not under a
`Drupal11/` tree:
- Rectors → `src/Rector/PHPUnit/`
- Value object → `src/Rector/PHPUnit/ValueObject/PhpUnitTestAnnotationToAttributeConfiguration.php`
- Tests → `tests/src/Rector/PHPUnit/<RectorName>/` (matching the existing
  `ShouldCallParentMethodsRector` layout)

**Why the PHPUnit space matters:** these rules are not Drupal-API deprecations — they are
generic PHPUnit modernizations. Keeping them self-contained in this namespace makes it
feasible to **contribute them upstream to `rector/rector-phpunit`** later. The only
Drupal-specific coupling is the BC/version gating (`DrupalRectorSettings`,
`installedDrupalVersion()`); design the conversion logic so that gating is the *only* thing
that would need swapping out for an upstream version.

Both reuse the existing versioned-configuration infrastructure (`AbstractDrupalCoreRector`
base, `VersionedConfigurationInterface` value objects, `PhpAttributeGroupFactory`,
`PhpDocInfo`/`PhpDocTagRemover`). No new BC machinery.

### Rector A — `PhpUnitTestAnnotationToAttributeRector` (configurable, annotation → attribute)

- **Node types:** `Class_` **and** `ClassMethod` (the existing `AnnotationToAttributeRector`
  is class-only; PHPUnit annotations are mostly method-level).
- **Guard:** only acts inside subclasses of `PHPUnit\Framework\TestCase` (same guard style as
  `ShouldCallParentMethodsRector`).
- **Driven by** a list of `PhpUnitTestAnnotationToAttributeConfiguration` value objects — one per
  mapping — each carrying `introducedVersion`, `removeVersion`, annotation tag, attribute FQN,
  and an optional value-conversion strategy.
- Attribute lands on the **same node** the annotation was on.

### Rector B — `PhpUnitAddRunTestsInSeparateProcessesAttributeRector` (additive)

- **Node type:** `Class_`.
- **Guard:** subclass of `KernelTestBase` **or** `BrowserTestBase` (i.e. not pure Unit tests —
  "added to all tests except unit tests"). Skips anonymous classes and classes that already
  carry the attribute.
- Purely additive: no source annotation, so the remove-axis (below) does not apply.
- Structurally equivalent to core's `FillRunTestInIsolationRector`.

## Backward-compatibility & version model

The decision *keep-both* (add attribute, keep `@annotation`) vs *attribute-only* (add +
remove `@annotation`) uses **both** axes combined:

```php
$removeAnnotation =
    !$this->drupalRectorSettings->isBackwardCompatibilityEnabled()
    || version_compare($this->installedDrupalVersion(), $configuration->getRemoveVersion(), '>=');
```

| Context | BC flag | installed | Result |
|---|---|---|---|
| Bot / classic D11 deprecation set, module supports <12 | on | 11.x | **Keep both** |
| Composer-based set (opted into clean rewrite) | off | any | Attribute-only |
| Any set on a D12 install / simulate-next-major | on/off | ≥12 | Attribute-only |

- `installedDrupalVersion()` and `isBackwardCompatibilityEnabled()` come from the base class
  (`AbstractDrupalCoreRector.php:134-140`, `:156`).
- **Rector A versions:** `introducedVersion = '11.0.0'`, `removeVersion = '12.0.0'`.
  Introducing early is safe (attribute is harmless on PHPUnit 9) and maximizes bot coverage.
- **This is the answer to "don't break BC with the bot":** the classic deprecation sets the
  bot runs keep BC **on**, so output keeps both and never forces a min-version bump. Only an
  explicit composer-set/clean-rewrite (BC off) strips annotations.

### Two correctness requirements the existing rector does not meet

1. **Emit the keep-both change.** The existing `AnnotationToAttributeRector` returns the node
   only when the annotation is removed (`AnnotationToAttributeRector.php:215`), so its
   keep-both state is a silent no-op. Rector A must `return $node` whenever it *added* an
   attribute, even if no annotation was removed.
2. **Idempotency.** Because keep-both leaves the `@annotation` in place, a second run must not
   append a duplicate attribute. Reuse the `$hasAttribute` pre-check
   (`AnnotationToAttributeRector.php:181-188`): if the target attribute is already present,
   add nothing (only consider removing the annotation per the rule above).

## Conversion table & edge cases (Rector A)

| `@annotation` | on | `#[Attribute]` | value handling |
|---|---|---|---|
| `@group X` | method/class | `#[Group('X')]` | string arg; **`@group legacy` → `#[IgnoreDeprecations]`** (no arg) |
| `@dataProvider m` | method | `#[DataProvider('m')]` | method-name string only |
| `@depends m` | method | `#[Depends('m')]` | plain method name only |
| `@testWith [json]` | method | one `#[TestWith([...])]` **per line** | parse each line's array literal |

- Attribute class emitted as `FullyQualified`; `use`/import cleanup left to Rector import
  config / phpcbf (matches existing `AnnotationToAttributeRector` behaviour).
- **Unsupported value forms are skipped** (annotation left untouched, no attribute added)
  rather than mis-converted — safer for an auto-run rule:
  - `@dataProvider Class::method` (external provider) → skip.
  - `@depends clone m` / `@depends !m` (modifier forms) → skip.
- A class can have both class-level and method-level `@group`; each is handled on its own node.

## Set registration

- Rector A → `config/drupal-11/drupal-11.0-deprecations.php`.
- Rector B → `config/drupal-11/drupal-11.4-deprecations.php` (`introducedVersion = '11.4.0'`;
  set placement and version gate kept in sync so composer-triggered cumulative loading and
  the runtime gate agree).
- `DrupalSetProvider` picks both up automatically for composer-based loads; the classic
  `drupal-11-all-deprecations.php` aggregate includes them via the per-minor files.

## Testing

Follow the existing two-class convention, extended to cover the OR-logic.

**Rector A** — `tests/src/Rector/PHPUnit/PhpUnitTestAnnotationToAttributeRector/`:
- `config/configured_rule.php` (BC on, 11.x) + `fixture/` → **keep-both** expected output.
- `config/configured_rule_simulate_next_major.php` (12.0) + `fixture-next-major/` →
  **attribute-only** expected output (the `BackwardsCompatibility…Test` class mocks
  `Drupal::VERSION`).
- `config/configured_rule_bc_disabled.php` (BC off, 11.x) → reuses attribute-only expected
  output, proving the BC-off branch of the OR independently of the version branch.
- Fixtures per mapping: `@group` (incl. `@group legacy`), `@dataProvider`, `@depends`,
  `@testWith` multiline, an idempotency fixture (attribute already present), and a
  non-`TestCase` negative fixture (no change).

**Rector B** — `tests/src/Rector/PHPUnit/PhpUnitAddRunTestsInSeparateProcessesAttributeRector/`:
- `fixture/` adds the attribute to a `KernelTestBase`/`BrowserTestBase` subclass.
- Negative fixtures: a `UnitTestCase` subclass (no change) and a class already carrying the
  attribute (idempotent).

## Documentation

- CHANGELOG.md `[Unreleased] / ### Added` entry for each rector (project cadence).
- Rule definitions (`getRuleDefinition`) with before/after `CodeSample`s.

## References (drupal.org / GitLab)

**Umbrella / meta issues**
- [#3527936](https://www.drupal.org/project/drupal/issues/3527936) — Introduce support for PHPUnit 12 (top-level driver).
- [#3535662](https://www.drupal.org/project/drupal/issues/3535662) — [meta] Convert test metadata from annotations to attributes.
- [#3445240](https://www.drupal.org/project/drupal/issues/3445240) — [meta] Add `#[RunTestsInSeparateProcesses]` to all Kernel and Functional tests.
- [#3217904](https://www.drupal.org/project/drupal/issues/3217904) — [meta] Support PHPUnit 10 in Drupal 11 (the upgrade that forced the annotation→attribute move; annotations and attributes cannot be mixed).

**The Rector rule this work is based on**
- [#3446380](https://www.drupal.org/project/drupal/issues/3446380) — *[no-commit] Define a Rector rule to convert test annotations to attributes*; the custom `DrupalAnnotationToAttributeRector` lives in GitLab MR [drupal!12218](https://git.drupalcode.org/project/drupal/-/merge_requests/12218). Extracted configs in `docs/d11-phpunit-investigation/rector-configs/`.
- [#3552124](https://git.drupalcode.org/project/rector/-/work_items/3552124) — *Leverage #3446380's rector rule to convert PHPUnit annotations to attributes* (the drupal-rector issue this branch implements; targets 11.3.x / 12.x).

**Per-feature core issues**
- `@group`/`@dataProvider`/`@depends`/`@testWith` → attributes (Rector A) — conversions landed across e.g. [#3534248](https://www.drupal.org/project/drupal/issues/3534248) (core Unit), [#3545163](https://www.drupal.org/project/drupal/issues/3545163) (core Kernel), [#3543586](https://www.drupal.org/project/drupal/issues/3543586) (modules' Kernel); enforcement of removal in [#3548982](https://www.drupal.org/project/drupal/issues/3548982) / [#3551681](https://www.drupal.org/project/drupal/issues/3551681).
- `#[RunTestsInSeparateProcesses]` (Rector B) — [#3548493](https://www.drupal.org/project/drupal/issues/3548493) / [#3546029](https://www.drupal.org/project/drupal/issues/3546029) (Kernel), [#3547849](https://www.drupal.org/project/drupal/issues/3547849) / [#3550335](https://www.drupal.org/project/drupal/issues/3550335) (Functional/FunctionalJavascript), [#3556315](https://www.drupal.org/project/drupal/issues/3556315) (follow-up fix to the enforcement approach).

**Deferred (out of scope) for context**
- [#3561671](https://www.drupal.org/project/drupal/issues/3561671) — [meta] Refactor tests to use stubs instead of mocks (PHPUnit 12.5; separate effort, not part of this work).

## Known limitations / future work

- `@covers`/`@uses` family deferred (see Scope).
- Rector B changes test *execution* on PHPUnit 10/11 (tests then run in separate processes),
  not only on D12 — accepted, matching core, but noted as a real behavior change for contrib.
- Build tests (`BuildTestBase`) are not covered by Rector B's `KernelTestBase`/`BrowserTestBase`
  guard; can be added if needed.
- **Upstream contribution:** keep the conversion logic decoupled from the Drupal BC gating so
  these rules can be proposed to `rector/rector-phpunit` later. Their existing PHPUnit set
  ([getrector.com phpunit set](https://getrector.com/find-rule?activeRectorSetGroup=phpunit))
  has partial coverage but no BC-preserving keep-both mode, which is the differentiator here.
