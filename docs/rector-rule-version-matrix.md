# Rule version matrix: deprecation in, removal out

**Generated:** 2026-09-20 · **Branch:** `pr-419-composer-based-set` · **Status:** both passes complete

Every drupal-rector rule with the Drupal version its deprecation landed in and
the version the old API was actually removed in. Built to answer one question:
can a rule carry a *bounded* composer constraint (`>=8.5.0 <9.0.0`) instead of
the open-ended `>=8.5.0` that PR #419 gives it?

Change records were the primary source, as asked. Where a record said nothing
or contradicted itself, the core symbol catalog settled it; where the catalog
had no row either, `repos/drupal-core` settled it directly.

## Method

**Pass 1 — change records.** Rule → introduction version from the per-minor
config that registers it. Rule → change records by scraping
`drupal.org/node/<nid>` links from the rule class *and* from the config line
registering it. CR → declared target from `change_record.change_to_branch`. CR →
removed API → removal version via `change_record_symbol` (`role = 'from'`) joined
to `core_symbol`.

**Pass 2 — the code.** Change records turned out to be unreliable (see below),
so the second pass went at the symbols directly:

1. **Per configuration entry, not per rule.** The configurable rules carry one
   deprecation per value object — 234 entries in all. Each object's constructor
   says which argument is the deprecated symbol, so the symbol was read from the
   argument rather than from prose. Two value objects
   (`DrupalServiceRenameConfiguration`, `FunctionToFirstArgMethodConfiguration`)
   exist in both a generic and a legacy `Drupal8`/`Drupal9` variant with
   different arities; the config's `use` statement disambiguates them.
2. **Symbol → removal** from `core_symbol.removal_in` / `removal_kind`.
3. **`repos/drupal-core` as the last resort** for what the catalog does not
   carry: core's own `@trigger_error` text, and `git log -S` plus
   `git tag --contains` to date a removal.

**Pass 3 — core's own promises, joined by change record.** Core states each
deprecation in a machine-readable form that *cites the change record*:

```
ModuleHandler::loadAllIncludes() is deprecated in drupal:11.3.0 and is
removed from drupal:13.0.0. … See https://www.drupal.org/node/3536432
```

There are 1,651 such strings in core, 912 of them carrying a node link, covering
217 distinct change records. Since every rule already has its change record
nids, `rule → nid → core's promise` is a join rather than research. It resolved
**17 of the 54 rules that still had no upper bound**, at no cost and with a
file:line citation for each.

These land as `scheduled`, not `observed` — Drupal 12 does not exist yet, so for
a Drupal 11 deprecation there is no removal to observe, only core's stated
intention.

**Pass 4 — researcher agents, with every citation re-checked.** The 27 rules the
join could not reach were handed to seven Haiku agents, four rules each, each
told to read the rule and its test fixtures, locate the deprecated symbol in
core, and return the notice **copied verbatim with a file and line**. A script
then re-read every citation and string-matched it. 18 of 21 bounded claims
verified; **3 were fabricated** — a cited file that does not exist, an
`evidence_text` that is not at the line given, and a version absent from the
text quoted. Those three were rejected and resolved by hand from core's git
history instead, which also closed two `not_found` results whose symbols were
removed before 11.0 and so are invisible in an 11.x working tree:

| Rule | Removed in | How |
|---|---|---|
| `ReplaceRequestTimeConstantRector` | 11.0.0 | `2f44d215e86` (#3442766), first tag 11.0.0 |
| `MigrateSqlGetMigrationPluginManagerRector` | 11.0.0 | `166f3a39e46` (#3439369), first tag 11.0.0 |
| `RemoveTwigNodeTransTagArgumentRector` | 11.1.0 | `cec21638d09` (#3477374), first tag 11.1.0 |
| `RenameStopProceduralHookScanRector` | 11.2.0 | `308ad151024` (#3495943), first tag 11.2.0 |
| `ReplaceLocaleTranslationPathConfigRector` | 13.0.0 | `locale.schema.yml:45`; the function form says 12.0.0 — take the widest |

Two lessons for any repeat run. The working tree only shows what is still
present, so a pre-11.0 removal needs `git log -S` plus `git tag --contains`, not
grep. And when a rule spans several notices with different removals, take the
**latest** — bounding on the earliest silently disables the rule while the
deprecation is still live.

`removal_kind` is the column that matters: **`observed`** means gone from the
tree — the code-level verification; **`scheduled`** means still present behind
an `@deprecated` promise, which core can still slip.

Data: `api.tresbien.tech`, `core_symbol` and `change_record` stamped 2026-09-19;
`repos/drupal-core` at `c4ec0e2c061` (11.x, 2026-09-19).

## Coverage

| Major | Rules | Removal version | Code-observed |
|---|---|---|---|
| Drupal8 | 17 | 16 | 16 |
| Drupal9 | 24 | 23 | 23 |
| Drupal10 | 6 | 5 | 3 |
| Drupal11 | 93 | 89 | 11 |
| Drupal12 | 1 | 0 | 0 |
| generic | 13 | 8 | 1 |
| **Total** | **154** | **141** | **54** |

Pass 1 alone reached 76 rules with a removal version and 39 observed; pass 2
took that to 100 and 48; passes 3 to 5 (below) to 141. Of the 234 configuration entries, **228 resolved to a
core symbol**. The 6 that did not are not core symbols at all: three
`GetMockConfiguration` entries name PHPUnit's `getMock()`, and three name
`Symfony\Cmf\Component\Routing\RouteObjectInterface` constants, which live in
`symfony-cmf/routing` (core stopped referencing them in 9.1.0, commit
`37893880799`, "Decouple from Symfony CMF").

D8 and D9 are now essentially complete, and almost entirely `observed`. D11 is
half-covered and almost entirely `scheduled` — which, as below, is the half that
should not be bounded anyway.

## Where the change records were wrong

You were right not to trust them. Seven are wrong, self-contradictory, or
unreadable:

| CR | Declared | Removal found | Evidence | Problem |
|---|---|---|---|---|
| [3519187](https://www.drupal.org/node/3519187) | 11.3.x | 11.3.0 | scheduled | Removal version equals the deprecation version |
| [3527501](https://www.drupal.org/node/3527501) | 11.2.x | 11.2.0 | observed | Same — and the symbol is already gone |
| [3570851](https://www.drupal.org/node/3570851) | 11.4.x | 11.4.0 | scheduled | Same |
| [3442229](https://www.drupal.org/node/3442229) | 11.1.x | 11.1.0 + 12.0.0 | mixed | One symbol removed in its own deprecation minor, another in 12.0.0 |
| [3349345](https://www.drupal.org/node/3349345) | 10.2.x | 10.5.0 | scheduled | Removal inside the same major |
| [3461934](https://www.drupal.org/node/3461934) | 10.4.x, 11.0.x | 11.1.0 | observed | Deprecated in 11.0, gone by 11.1 — one minor of grace |
| [3513877](https://www.drupal.org/node/3513877) | `11.3,x` | — | — | Typo in the record's own branch field (comma for a dot) |

And the single most useful result of pass 2, which no change record would have
told us:

> **`RequestTimeConstRector` sits in the 8.3 set, but `REQUEST_TIME` was not
> removed until 11.0.0** — `observed`, confirmed in the catalog.

A bound computed as "deprecation major + 1" would have given it `<9.0.0` and
silently switched the rule off for every Drupal 9 and 10 user, for a constant
that was still there and still deprecated. Together with 3349345 (10.2 → 10.5.0)
and 3461934 (11.0 → 11.1.0), that is three independent proofs of the same rule:

- **Derive the upper bound from `removal_in`. Never from arithmetic on the
  deprecation version.**
- **Prefer `observed` over `scheduled`.** A scheduled removal is a promise, and
  bounding on a promise turns a slipped removal into a silently inactive rule.

## Drupal 8 / 9: the bounding candidates

**38 of 43 rules can be bounded, every one of them on an `observed` removal.**
Pass 1 managed 15; resolving the symbols directly closed the rest.

The 9.1 `AssertLegacyTrait` block — 12 rules that cited no change record at all —
resolved in a single lookup: every method of `Drupal\FunctionalTests\AssertLegacyTrait`
and `Drupal\Tests\UiHelperTrait::drupalPostForm` was removed in **10.0.0**,
`observed`.

| Rule | Set | Removed in | Evidence | Evidence-backed bound |
|---|---|---|---|---|
| `AssertFieldByIdRector` | 9.1 | 10.0.0 | observed | `>=9.1.0 <10.0.0` |
| `AssertFieldByNameRector` | 9.1 | 10.0.0 | observed | `>=9.1.0 <10.0.0` |
| `AssertLegacyTraitRector` | 9.1 | 10.0.0 | observed | `>=9.1.0 <10.0.0` |
| `AssertNoFieldByIdRector` | 9.1 | 10.0.0 | observed | `>=9.1.0 <10.0.0` |
| `AssertNoFieldByNameRector` | 9.1 | 10.0.0 | observed | `>=9.1.0 <10.0.0` |
| `AssertNoUniqueTextRector` | 9.1 | 10.0.0 | observed | `>=9.1.0 <10.0.0` |
| `AssertOptionSelectedRector` | 9.1 | 10.0.0 | observed | `>=9.1.0 <10.0.0` |
| `ConstructFieldXpathRector` | 9.1 | 10.0.0 | observed | `>=9.1.0 <10.0.0` |
| `DBRector` | 8.0 | 9.0.0 | observed | `>=8.0.0 <9.0.0` |
| `DrupalLRector` | 8.0 | 9.0.0 | observed | `>=8.0.0 <9.0.0` |
| `DrupalServiceRenameRector` | 8.8 | 9.0.0 | observed | `>=8.8.0 <9.0.0` |
| `DrupalSetMessageRector` | 8.5 | 9.0.0 | observed | `>=8.5.0 <9.0.0` |
| `DrupalURLRector` | 8.0 | 9.0.0 | observed | `>=8.0.0 <9.0.0` |
| `EntityCreateRector` | 8.0 | 9.0.0 | observed | `>=8.0.0 <9.0.0` |
| `EntityDeleteMultipleRector` | 8.0 | 9.0.0 | observed | `>=8.0.0 <9.0.0` |
| `EntityInterfaceLinkRector` | 8.0 | 9.0.0 | observed | `>=8.0.0 <9.0.0` |
| `EntityLoadRector` | 8.0 | 9.0.0 | observed | `>=8.0.0 <9.0.0` |
| `EntityManagerRector` | 8.0 | 9.0.0 | observed | `>=8.0.0 <9.0.0` |
| `EntityViewRector` | 8.0 | 9.0.0 | observed | `>=8.0.0 <9.0.0` |
| `ExtensionPathRector` | 9.3 | 10.0.0 | observed | `>=9.3.0 <10.0.0` |
| `FileBuildUriRector` | 9.3 | 10.0.0 | observed | `>=9.3.0 <10.0.0` |
| `FileCreateUrlRector` | 9.3 | 10.0.0 | observed | `>=9.3.0 <10.0.0` |
| `FileDefaultSchemeRector` | 8.8 | 9.0.0 | observed | `>=8.8.0 <9.0.0` |
| `FileUrlTransformRelativeRector` | 9.3 | 10.0.0 | observed | `>=9.3.0 <10.0.0` |
| `FromUriRector` | 9.3 | — | — | — behaviour change, no removal |
| `FunctionToFirstArgMethodRector` | 9.3 11.2 11.3 | MULTI | 2 distinct | — per entry, not per class |
| `FunctionalTestDefaultThemePropertyRector` | 8.8 | — | — | — behaviour change, no removal |
| `GetAllOptionsRector` | 9.1 | 10.0.0 | observed | `>=9.1.0 <10.0.0` |
| `GetMockRector` | 8.4 | — | — | — behaviour change, no removal |
| `GetRawContentRector` | 9.1 | 10.0.0 | observed | `>=9.1.0 <10.0.0` |
| `LinkGeneratorTraitLRector` | 8.0 | 9.0.0 | observed | `>=8.0.0 <9.0.0` |
| `ModuleLoadRector` | 9.4 | 10.0.0 | observed | `>=9.4.0 <10.0.0` |
| `PassRector` | 9.1 | 10.0.0 | observed | `>=9.1.0 <10.0.0` |
| `ProtectedStaticModulesPropertyRector` | 9.0 | — | — | — behaviour change, no removal |
| `RequestTimeConstRector` | 8.3 | 11.0.0 | observed | `>=8.3.0 <11.0.0` |
| `SafeMarkupFormatRector` | 8.0 | 9.0.0 | observed | `>=8.0.0 <9.0.0` |
| `StaticToFunctionRector` | 8.6 | 9.0.0 | observed | `>=8.6.0 <9.0.0` |
| `SystemSortByInfoNameRector` | 9.3 | 10.0.0 | observed | `>=9.3.0 <10.0.0` |
| `TaxonomyTermLoadMultipleByNameRector` | 9.3 | 10.0.0 | observed | `>=9.3.0 <10.0.0` |
| `TaxonomyVocabularyGetNamesDrupalStaticResetRector` | 9.3 | 10.0.0 | observed | `>=9.3.0 <10.0.0` |
| `TaxonomyVocabularyGetNamesRector` | 9.3 | 10.0.0 | observed | `>=9.3.0 <10.0.0` |
| `UiHelperTraitDrupalPostFormRector` | 9.1 | 10.0.0 | observed | `>=9.1.0 <10.0.0` |
| `UserPasswordRector` | 9.1 | 10.0.0 | observed | `>=9.1.0 <10.0.0` |
The five that cannot be bounded are correct to stay open-ended — they target a
*behaviour* change rather than a removed symbol:

- `FromUriRector` — `Drupal\Core\Url::fromUri()` still exists; the change is to
  its argument handling.
- `ProtectedStaticModulesPropertyRector` — a visibility change on `$modules`.
- `FunctionalTestDefaultThemePropertyRector` — `$defaultTheme` became required.
- `GetMockRector` — targets PHPUnit's `getMock()`, so the rule is bound to
  `phpunit/phpunit`, not to `drupal/core`. Worth noting on its own: its current
  `drupal/core` constraint describes the wrong package.
- `FunctionToFirstArgMethodRector` — see below.

## Why bounds belong per entry, not per class

Aggregating the 228 resolved entries by the rule that registers them shows the
generic configurable rules each span many removals at once:

| Rule | Entries | Distinct removals |
|---|---|---|
| `FunctionToServiceRector` | 97 | 9.0.0 → 13.0.0, observed and scheduled |
| `FunctionToStaticRector` | 20 | 9.0.0 → 13.0.0 |
| `ConstantToClassConstantRector` | 14 | 9.0.0 → 13.0.0 |
| `FunctionCallRemovalRector` | 16 | 12.0.0 → 13.0.0 |
| `ClassConstantToClassConstantRector` | 14 | 12.0.0 → 13.0.0 |
| `MethodToMethodWithCheckRector` | 8 | 9.0.0 → 12.0.0 |
| `FunctionToFirstArgMethodRector` | 5 | 10.0.0, 12.0.0 |
| `AssertLegacyTraitRector` | 36 | 10.0.0 only |
| `DBRector` | 5 | 9.0.0 only |
| `DrupalServiceRenameRector` | 5 | 9.0.0 only |
| `StaticToFunctionRector` | 3 | 9.0.0 only |
| `ExtensionPathRector` | 2 | 10.0.0 only |
| `FunctionToEntityTypeStorageMethod` | 2 | 10.0.0 only |
| `RenameStaticMethodRector` | 1 | 10.0.0 only |

No single class-level constraint can be right for the first seven. Their bounds
have to live per configuration entry — which is what
`ruleWithConfigurationComposerVersionBound()` already does, and another reason
the class-level `ComposerPackageConstraintInterface` is the wrong home for a
bound. The last seven are single-purpose and take a class-level bound cleanly.

## On bounding Drupal 11 at all

Drupal 11's bounds are almost all `scheduled` rather than `observed` — 89 of its
93 rules now carry a removal version, but only 11 are code-observed, because
Drupal 12 does not exist yet. Everything else is core's stated intention for
12.0.0 or 13.0.0.

That is not a reason to leave them unbounded, but it is a reason to re-check
them. A promise that slips turns a bounded rule into a silently inactive one,
and the same change-record join that produced these bounds re-runs in seconds —
worth wiring into CI so a moved promise surfaces as a notification rather than
as a rule that quietly stops firing.

## Pass 5 — chasing the residue through core, the catalog and drupal.org

Pass 4's leftovers were not all dead ends. Checking each against the symbol
catalog, core's git history and the change records on drupal.org turned two of
them into real bounds and corrected the package on a third.

**`FromUriRector` was a misidentified symbol, not a missing one.** Pass 2 looked
up `Drupal\Core\Url::fromUri`, found it undeprecated, and filed the rule as a
behaviour change. The rule actually matches `Url::fromUri(file_create_url($uri))`
and rewrites it to `\Drupal::service('file_url_generator')->generate()`. Its
deprecated symbol is `file_create_url()` — deprecated 9.3.0, **removed 10.0.0,
observed**. Bound: `>=9.3.0 <10.0.0`. It has no test fixtures, which is why
fixture-based identification could not reach it either.

**`FunctionalTestDefaultThemePropertyRector` has a removal after all.**
Deprecated in 8.8.0 ([CR 3083055](https://www.drupal.org/node/3083055)); the BC
layer came out in **9.0.0**, commit `305c401f90e` (#3110874, "Remove BC layer for
TestSetupTrait"). Core now throws, citing that very change record:

```php
throw new \Exception('Drupal\Tests\BrowserTestBase::$defaultTheme is required.
  See https://www.drupal.org/node/3083055, ...');
```

Bound: `>=8.8.0 <9.0.0`.

**A sixth wrong-package rule.** `AddSymfonyConstraintValidatorTypeDeclarationsRector`
links `symfony/symfony` `blob/8.0/…/ConstraintValidatorInterface.php` in its own
docblock: it tracks **Symfony 8.0**, not `drupal/core`. PR #419 gives it
`>=11.0.0` against `drupal/core`.

Incidental, and another trap for anyone computing bounds by arithmetic: Classy
was removed in **10.1.0**, not 10.0.0 (`c3d1caafb5a`, #3110137).

## The 13 rules that still have no upper bound — and should not get one

None is an unfinished lookup; each was chased to a citation.

**Wrong package (6).** A `drupal/core` bound is meaningless for these, and PR
#419 gives five of them one anyway — a defect worth reporting on its own.

| Rule | Actually bound to | Evidence |
|---|---|---|
| `GetMockRector` | `phpunit/phpunit <6.0` | [CR 2907725](https://www.drupal.org/node/2907725): deprecated in Drupal 8.4.4, "getMock() is removed in PHPUnit 6". No Drupal removal version. |
| `GetNameToNameRector` | `phpunit/phpunit >=10.0` | [CR 3217904](https://www.drupal.org/node/3217904): `TestCase::getName` was renamed to `name` in PHPUnit 10. The rule type-checks `PHPUnit\Framework\TestCase`. |
| `PhpUnitTestAnnotationToAttributeRector` | `phpunit/phpunit` | Annotations deprecated in PHPUnit 11, removed in PHPUnit 12. |
| `PhpUnitAddRunTestsInSeparateProcessesAttributeRector` | `phpunit/phpunit` | PHPUnit attribute; cites core issue #3445240. |
| `RemovePhpUnitCompatibilityTraitRector` | `phpunit/phpunit` | [#3582118](https://www.drupal.org/node/3582118): the trait became "a no-op … since PHPUnit 11". Still present at 11.x HEAD with no deprecation notice. |
| `AddSymfonyConstraintValidatorTypeDeclarationsRector` | `symfony/validator ^8.0` | Its own `@see` points at Symfony 8.0's `ConstraintValidatorInterface`. |

**Nothing is removed (4).**

- `ViewsConfigUpdaterClassResolverToServiceRector` — settled, not merely
  plausible: `\Drupal::classResolver` is `added_in: 8.3.x`, `status: []`, with no
  `deprecated_in` and no `removal_in`. [CR 3530638](https://www.drupal.org/node/3530638)
  ("ViewsConfigUpdater is now a service") recommends `\Drupal::service()` over
  `\Drupal::classResolver()`; the old call still works.
- `ProtectedStaticModulesPropertyRector` — `$modules` became `protected static`
  in **8.3.0** (`b33af7a964e`, #2814035). A visibility change with no removal.
- `RemoveStateCacheSettingRector` — the rule cites 3436954 and 2575105, but core
  cites [3177901](https://www.drupal.org/node/3177901), which says *"In Drupal
  11+, settings 'state_cache' is removed and permanently turned on"*. That is the
  rule's **lower** bound, not an upper one: the setting is inert from 11.0.0, and
  `lib/Drupal/Core/Site/Settings.php:42` still flags it with no removal deadline.
  Indefinite cleanup.
- `ShouldCallParentMethodsRector` — not a deprecation rule at all. Its own
  definition reads *"PHPUnit based tests should call parent methods (setUp,
  tearDown)"*. It is test hygiene, registered in the 9.0 and 10.0 **deprecation**
  sets, which is arguably a miscategorisation worth raising separately.

**Infrastructure (3).** No deprecation, no set, nothing to bound:
`HookConvertRector`, `DeprecationHelperRemoveRector`, `AnnotationToAttributeRector`.

## Full matrix

`Set` is the per-minor config that registers the rule — drupal-rector's own
claim about when the deprecation landed; `*` marks a breaking set. `Source` says
which pass produced the removal version.

| Rule | Set | Class constraint | Removed in | Evidence | Source |
|---|---|---|---|---|---|
| `DBRector` | 8.0 | config-bound | 9.0.0 | observed | config entries |
| `DrupalLRector` | 8.0 | `>=8.0.0` | 9.0.0 | observed | symbol |
| `DrupalServiceRenameRector` | 8.8 | config-bound | 9.0.0 | observed | config entries |
| `DrupalSetMessageRector` | 8.5 | `>=8.5.0` | 9.0.0 | observed | change record |
| `DrupalURLRector` | 8.0 | `>=8.0.0` | 9.0.0 | observed | symbol |
| `EntityCreateRector` | 8.0 | `>=8.0.0` | 9.0.0 | observed | change record |
| `EntityDeleteMultipleRector` | 8.0 | `>=8.0.0` | 9.0.0 | observed | change record |
| `EntityInterfaceLinkRector` | 8.0 | `>=8.0.0` | 9.0.0 | observed | change record |
| `EntityLoadRector` | 8.0 | config-bound | 9.0.0 | observed | change record |
| `EntityManagerRector` | 8.0 | `>=8.0.0` | 9.0.0 | observed | change record |
| `EntityViewRector` | 8.0 | `>=8.0.0` | 9.0.0 | observed | symbol |
| `FileDefaultSchemeRector` | 8.8 | `>=8.8.0` | 9.0.0 | observed | change record |
| `FunctionalTestDefaultThemePropertyRector` | 8.8 | `>=8.8.0` | 9.0.0 | observed | core git 305c401f90e |
| `GetMockRector` | 8.4 | config-bound | — | — | — |
| `LinkGeneratorTraitLRector` | 8.0 | `>=8.0.0` | 9.0.0 | observed | change record |
| `RequestTimeConstRector` | 8.3 | `>=8.3.0` | 11.0.0 | observed | symbol |
| `SafeMarkupFormatRector` | 8.0 | `>=8.0.0` | 9.0.0 | observed | change record |
| `StaticToFunctionRector` | 8.6 | config-bound | 9.0.0 | observed | config entries |
| `AssertFieldByIdRector` | 9.1 | `>=9.1.0` | 10.0.0 | observed | symbol |
| `AssertFieldByNameRector` | 9.1 | `>=9.1.0` | 10.0.0 | observed | symbol |
| `AssertLegacyTraitRector` | 9.1 | config-bound | 10.0.0 | observed | config entries |
| `AssertNoFieldByIdRector` | 9.1 | `>=9.1.0` | 10.0.0 | observed | symbol |
| `AssertNoFieldByNameRector` | 9.1 | `>=9.1.0` | 10.0.0 | observed | symbol |
| `AssertNoUniqueTextRector` | 9.1 | `>=9.1.0` | 10.0.0 | observed | symbol |
| `AssertOptionSelectedRector` | 9.1 | `>=9.1.0` | 10.0.0 | observed | symbol |
| `ConstructFieldXpathRector` | 9.1 | `>=9.1.0` | 10.0.0 | observed | symbol |
| `ExtensionPathRector` | 9.3 | config-bound | 10.0.0 | observed | config entries |
| `FileBuildUriRector` | 9.3 | `>=9.3.0` | 10.0.0 | observed | change record |
| `FileCreateUrlRector` | 9.3 | `>=9.3.0` | 10.0.0 | observed | symbol |
| `FileUrlTransformRelativeRector` | 9.3 | `>=9.3.0` | 10.0.0 | observed | symbol |
| `FromUriRector` | 9.3 | `>=9.3.0` | 10.0.0 | observed | fn:file_create_url |
| `FunctionToFirstArgMethodRector` | 9.3 11.2 11.3 | config-bound | several | 2 distinct | config entries |
| `GetAllOptionsRector` | 9.1 | `>=9.1.0` | 10.0.0 | observed | symbol |
| `GetRawContentRector` | 9.1 | `>=9.1.0` | 10.0.0 | observed | symbol |
| `ModuleLoadRector` | 9.4 | `>=9.4.0` | 10.0.0 | observed | change record |
| `PassRector` | 9.1 | `>=9.1.0` | 10.0.0 | observed | symbol |
| `ProtectedStaticModulesPropertyRector` | 9.0 | `>=9.0.0` | — | — | — |
| `SystemSortByInfoNameRector` | 9.3 | `>=9.3.0` | 10.0.0 | observed | change record |
| `TaxonomyTermLoadMultipleByNameRector` | 9.3 | `>=9.3.0` | 10.0.0 | observed | symbol |
| `TaxonomyVocabularyGetNamesDrupalStaticResetRector` | 9.3 | `>=9.3.0` | 10.0.0 | observed | symbol |
| `TaxonomyVocabularyGetNamesRector` | 9.3 | `>=9.3.0` | 10.0.0 | observed | symbol |
| `UiHelperTraitDrupalPostFormRector` | 9.1 | `>=9.1.0` | 10.0.0 | observed | symbol |
| `UserPasswordRector` | 9.1 | `>=9.1.0` | 10.0.0 | observed | symbol |
| `AnnotationToAttributeRector` | — | config-bound | — | — | — |
| `ReplaceModuleHandlerGetNameRector` | 10.3 | config-bound | 12.0.0 | scheduled | change record |
| `ReplaceRebuildThemeDataRector` | 10.3 | config-bound | 12.0.0 | scheduled | change record |
| `ReplaceRequestTimeConstantRector` | 11.0 | config-bound | 11.0.0 | observed | core git 2f44d215e86 |
| `SystemTimeZonesRector` | 10.1 | config-bound | 11.0.0 | observed | change record |
| `WatchdogExceptionRector` | 10.1 | config-bound | 11.0.0 | observed | change record |
| `BlockContentSelectionExtendsRector` | 11.4* | `>=11.4.0` | 12.0.0 | scheduled | core promise |
| `BlockContentTestBaseStringToArrayRector` | 11.1 | `>=11.1.0` | 12.0.0 | scheduled | core promise |
| `CheckMarkupToProcessedTextRector` | 11.4 | `>=11.4.0` | 13.0.0 | scheduled | symbol |
| `CommentLinkBuilderConstructorRector` | 11.3 | config-bound | 12.0.0 | scheduled | core notice |
| `DeprecatedFilterFunctionsRector` | 11.4 | config-bound | 12.0.0 | scheduled/observed | change record |
| `DrupalGetHeadersAssocArrayRector` | 11.1 | `>=11.1.0` | 12.0.0 | scheduled | core notice |
| `EntityFormModeEmptyDescriptionToNullRector` | 11.2 | `>=11.2.0` | 12.0.0 | scheduled | core notice |
| `ErrorCurrentErrorHandlerRector` | 11.3 | config-bound | 13.0.0 | scheduled | change record |
| `FileManagedFileSubmitRector` | 11.3 | config-bound | 12.0.0 | scheduled | core promise |
| `FileSystemBasenameToNativeRector` | 11.3 | config-bound | 13.0.0 | scheduled | change record |
| `FilterFormatFunctionsToServiceRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `GetDrupalRootToRootPropertyRector` | 11.4 | `>=11.4.0` | 13.0.0 | scheduled | core promise |
| `GetNameToNameRector` | 11.0 | `>=11.0.0` | — | — | — |
| `GetOriginalClassToGetDecoratedClassesRector` | 11.4 | config-bound | 12.0.0 | scheduled | core notice |
| `HookRequirementsAlterRenameRector` | 11.3* | `>=11.3.0` | 13.0.0 | scheduled | core notice |
| `LoadAllIncludesRector` | 11.3 | `>=11.3.0` | 13.0.0 | scheduled | core promise |
| `LocaleCompareIncToServiceRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `MediaFilterFormatEditFormValidateRector` | 11.4 | config-bound | 12.0.0 | scheduled/observed | change record |
| `MigrateSqlGetMigrationPluginManagerRector` | 11.0 | config-bound | 11.0.0 | observed | core git 166f3a39e46 |
| `MovePointerToMouseOverRector` | 11.1 | `>=11.1.0` | 12.0.0 | scheduled | core promise |
| `NodeAccessRebuildFunctionsRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `NodeStorageDeprecatedMethodsRector` | 11.3 | `>=11.3.0` | 11.3.0 | scheduled | change record |
| `PluginBaseIsConfigurableRector` | 11.1 | config-bound | 11.0.0 | observed | change record |
| `RemoveAutomatedCronSubmitHandlerRector` | 11.4 | `>=11.4.0` | 12.0.0 | scheduled/observed | change record |
| `RemoveCacheExpireOverrideRector` | 11.4 | `>=11.4.0` | 13.0.0 | scheduled | change record |
| `RemoveCacheTagChecksumAssertionsRector` | 11.2 | `>=11.2.0` | 12.0.0 | observed | change record |
| `RemoveConfigSaveTrustedDataArgRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `RemoveDrupalToStringTraitRector` | 11.4 | `>=11.4.0` | 13.0.0 | scheduled | change record |
| `RemoveFilterTipsLongParamRector` | 11.4 11.4* | `>=11.4.0` | 12.0.0 | scheduled | symbol |
| `RemoveHandlerBaseDefineExtraOptionsRector` | 11.2 | `>=11.2.0` | 12.0.0 | scheduled | change record |
| `RemoveInstallSchemaSystemSequencesRector` | 11.4 | `>=11.4.0` | 10.5.0 | scheduled | change record |
| `RemoveLinkWidgetValidateTitleElementRector` | 11.4 | `>=11.4.0` | 12.0.0 | scheduled | change record |
| `RemoveModuleHandlerAddModuleCallsRector` | 11.2 | `>=11.2.0` | 12.0.0 | scheduled | change record |
| `RemoveModuleHandlerDeprecatedMethodsRector` | 11.1 | `>=11.1.0` | 12.0.0 | scheduled | change record |
| `RemovePhpUnitCompatibilityTraitRector` | 11.4 | config-bound | — | — | — |
| `RemoveRendererAddCacheableDependencyNonObjectRector` | 11.3 | `>=11.3.0` | 11.2.0 | scheduled/observed | change record |
| `RemoveRootFromConvertDbUrlRector` | 11.3 | config-bound | 12.0.0 | scheduled | core notice |
| `RemoveRootFromCreateConnectionOptionsFromUrlRector` | 11.2 | `>=11.2.0` | 12.0.0 | scheduled | core notice |
| `RemoveRouteBuilderDeprecatedArgsRector` | 11.4 | config-bound | 12.0.0 | scheduled | core notice |
| `RemoveSetUriCallbackRector` | 11.4 | `>=11.4.0` | 13.0.0 | scheduled | core promise |
| `RemoveSourceModuleFromMigrateSourceAttributeRector` | 11.2* | `>=11.2.0` | 12.0.0 | scheduled | core notice |
| `RemoveStateCacheSettingRector` | 11.0 | `>=11.0.0` | — | — | — |
| `RemoveToolkitArgFromImageToolkitOperationConstructorRector` | 11.4 | `>=11.4.0` | 13.0.0 | scheduled | core notice |
| `RemoveTrustDataCallRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `RemoveTwigNodeTransTagArgumentRector` | 11.2 | config-bound | 11.1.0 | observed | core git cec21638d09 |
| `RemoveUpdaterPostInstallMethodsRector` | 11.1 | `>=11.1.0` | 11.1.0 | observed | change record |
| `RemoveViewsRowCacheKeysRector` | 11.4 | `>=11.4.0` | 13.0.0 | scheduled | change record |
| `RenameHookRankingRector` | 11.3* | `>=11.3.0` | 12.0.0 | observed | change record |
| `RenameStopProceduralHookScanRector` | 11.2 | `>=11.2.0` | 11.2.0 | observed | core git 308ad151024 |
| `ReplaceAddCachedDiscoveryMethodCallRector` | 11.1 | config-bound | 11.1.0 | scheduled/observed | change record |
| `ReplaceAlphadecimalToIntNullRector` | 11.2 | config-bound | 12.0.0 | scheduled | core notice |
| `ReplaceCommentManagerGetCountNewCommentsRector` | 11.3 | config-bound | 12.0.0 | observed | symbol |
| `ReplaceCommentPreviewConstantsRector` | 11.3 | config-bound | 13.0.0 | scheduled | core notice |
| `ReplaceDateTimeRangeConstantsRector` | 11.2 | config-bound | 12.0.0 | scheduled | change record |
| `ReplaceDialogClassOptionRector` | 11.3 | `>=11.3.0` | 12.0.0 | scheduled | core notice |
| `ReplaceDrupalStaticResetFileReferencesRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `ReplaceEditorLoadRector` | 11.2 | config-bound | 12.0.0 | observed | symbol |
| `ReplaceEntityOriginalPropertyRector` | 11.2 | config-bound | 12.0.0 | scheduled | core promise |
| `ReplaceEntityReferenceRecursiveLimitRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `ReplaceExpectDeprecationRector` | 11.4 | config-bound | 12.0.0 | scheduled | core promise |
| `ReplaceFieldgroupToFieldsetRector` | 11.2 | config-bound | 12.0.0 | scheduled | core promise |
| `ReplaceHideShowWithPrintedRector` | 11.4 | `>=11.4.0` | 13.0.0 | scheduled | change record |
| `ReplaceItemAttributesWithAttributesRector` | 11.4 | config-bound | 12.0.0 | scheduled | core promise |
| `ReplaceLocaleBatchProceduralFunctionsRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `ReplaceLocaleConfigBatchFunctionsRector` | 11.1 | config-bound | 12.0.0 | observed/scheduled | change record |
| `ReplaceLocaleTranslationPathConfigRector` | 11.4 | config-bound | 13.0.0 | scheduled | locale.schema.yml:45 |
| `ReplaceNodeAccessViewAllNodesRector` | 11.3 | config-bound | 12.0.0 | observed | symbol |
| `ReplaceNodeAddBodyFieldRector` | 11.3 | config-bound | 12.0.0 | observed | symbol |
| `ReplaceNodeModuleProceduralFunctionsRector` | 11.3 | config-bound | 13.0.0 | scheduled | change record |
| `ReplaceNodeSetPreviewModeRector` | 11.3 | config-bound | 13.0.0 | scheduled | core notice |
| `ReplaceNodeViewControllerRector` | 11.4* | `>=11.4.0` | 12.0.0 | scheduled/observed | change record |
| `ReplaceNonBoolAccessRector` | 11.4 | `>=11.4.0` | 13.0.0 | scheduled | core promise |
| `ReplacePdoFetchConstantsRector` | 11.2 | config-bound | 12.0.0 | scheduled | core promise |
| `ReplaceRecipeRunnerInstallModuleRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `ReplaceSessionManagerDeleteRector` | 11.4 | config-bound | 11.4.0 | scheduled | change record |
| `ReplaceSessionWritesWithRequestSessionRector` | 11.2 | config-bound | 12.0.0 | scheduled | core notice |
| `ReplaceSystemPerformanceGzipKeyRector` | 11.4 | config-bound | 12.0.0 | scheduled | core promise |
| `ReplaceThemeGetSettingRector` | 11.3 | config-bound | 13.0.0 | scheduled | change record |
| `ReplaceTwigExtensionRector` | 11.3 | config-bound | 12.0.0 | scheduled | core promise |
| `ReplaceUserOneTimeAuthFunctionsRector` | 11.4 | config-bound | 12.0.0 | scheduled | change record |
| `ReplaceUserSessionNamePropertyRector` | 11.3 | config-bound | 12.0.0 | scheduled | core promise |
| `ReplaceViewsProceduralFunctionsRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `StatementPrefetchIteratorFetchColumnRector` | 11.2 | config-bound | 12.0.0 | scheduled | change record |
| `StripMigrationDependenciesExpandArgRector` | 11.0 | config-bound | 12.0.0 | scheduled | core notice |
| `SystemRegionFunctionsRector` | 11.4 | config-bound | 12.0.0 | scheduled | change record |
| `SystemSortThemesRector` | 11.4 | `>=11.4.0` | 12.0.0 | scheduled/observed | change record |
| `TaxonomyTermPageVariableToViewModeRector` | 11.3 | `>=11.3.0` | 13.0.0 | scheduled | change record |
| `UploadedFileConstraintArrayOptionsToNamedArgsRector` | 11.4 | config-bound | 12.0.0 | scheduled | core notice |
| `UseEntityTypeHasIntegerIdRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `UserLoadByNameAndMailRector` | 11.4 | `>=11.4.0` | 13.0.0 | scheduled | change record |
| `ViewsBlockItemsPerPageNoneToNullRector` | 11.2 | `>=11.2.0` | 12.0.0 | scheduled | core notice |
| `ViewsConfigUpdaterClassResolverToServiceRector` | 11.3 | config-bound | — | — | — |
| `ViewsPluginHandlerManagerRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `AddSymfonyConstraintValidatorTypeDeclarationsRector` | 11.0 12.0 | `>=11.0.0` | — | — | — |
| `ClassConstantToClassConstantRector` | 9.1 10.3 11.2 11.4 | config-bound | several | 2 distinct | config entries |
| `ConstantToClassConstantRector` | 8.5 8.7 9.3 11.2 11.3 11.4 | config-bound | several | 4 distinct | config entries |
| `DeprecationHelperRemoveRector` | — | config-bound | — | — | — |
| `FunctionCallRemovalRector` | 11.2 11.3 11.4 | config-bound | several | 3 distinct | config entries |
| `FunctionToServiceRector` | 8.0 8.7 8.8 9.3 10.2 11.2 11.3 11.4 | config-bound | several | 7 distinct | config entries |
| `FunctionToStaticRector` | 8.2 9.3 10.1 10.2 10.3 11.1 11.2 11.3 11.4 | config-bound | several | 6 distinct | config entries |
| `HookConvertRector` | — | config-bound | — | — | — |
| `MethodToMethodWithCheckRector` | 8.0 8.8 9.2 10.2 10.3 11.1 11.2 | config-bound | several | 4 distinct | config entries |
| `PhpUnitAddRunTestsInSeparateProcessesAttributeRector` | 11.4 | config-bound | — | — | — |
| `PhpUnitTestAnnotationToAttributeRector` | 11.0 | config-bound | — | — | — |
| `ShouldCallParentMethodsRector` | 9.0 10.0 | `>=9.0.0` | — | — | — |