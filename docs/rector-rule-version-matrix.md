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

`removal_kind` is the column that matters: **`observed`** means gone from the
tree — the code-level verification; **`scheduled`** means still present behind
an `@deprecated` promise, which core can still slip.

Data: `api.tresbien.tech`, `core_symbol` and `change_record` stamped 2026-09-19;
`repos/drupal-core` at `c4ec0e2c061` (11.x, 2026-09-19).

## Coverage

| Major | Rules | Removal version | Code-observed |
|---|---|---|---|
| Drupal8 | 18 | 16 | 16 |
| Drupal9 | 25 | 23 | 22 |
| Drupal10 | 6 | 4 | 2 |
| Drupal11 | 93 | 51 | 8 |
| Drupal12 | 1 | 0 | 0 |
| generic | 11 | 6 | 0 |
| **Total** | **154** | **100** | **48** |

Pass 1 alone reached 76 rules with a removal version and 39 observed; pass 2
took that to 100 and 48. Of the 234 configuration entries, **228 resolved to a
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

## What is left

- **42 Drupal 11 rules have no removal version**, because the change records
  they cite either are not indexed (130 of the 275 records our rules reference
  resolve to no row) or link no `from` symbol. Closing them would need symbol
  extraction from each rule body, and the payoff is low: of the 51 D11 rules
  that *do* have a removal version, only 8 are `observed` — the rest are
  `scheduled` for 12.0.0 or 13.0.0, and bounding on a promise is the thing the
  evidence above says not to do. **Recommend stopping here for D11** until those
  removals actually land.
- **The generic rules' 6 remaining entries** are not core symbols (PHPUnit and
  Symfony CMF) and never will resolve against `core_symbol`.

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
| `FunctionalTestDefaultThemePropertyRector` | 8.8 | `>=8.8.0` | — | — | — |
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
| `FromUriRector` | 9.3 | `>=9.3.0` | — | — | — |
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
| `ReplaceRequestTimeConstantRector` | 11.0 | config-bound | — | — | — |
| `SystemTimeZonesRector` | 10.1 | config-bound | 11.0.0 | observed | change record |
| `WatchdogExceptionRector` | 10.1 | config-bound | 11.0.0 | observed | change record |
| `BlockContentSelectionExtendsRector` | 11.4* | `>=11.4.0` | — | — | — |
| `BlockContentTestBaseStringToArrayRector` | 11.1 | `>=11.1.0` | — | — | — |
| `CheckMarkupToProcessedTextRector` | 11.4 | `>=11.4.0` | 13.0.0 | scheduled | symbol |
| `CommentLinkBuilderConstructorRector` | 11.3 | config-bound | — | — | — |
| `DeprecatedFilterFunctionsRector` | 11.4 | config-bound | 12.0.0 | scheduled/observed | change record |
| `DrupalGetHeadersAssocArrayRector` | 11.1 | `>=11.1.0` | — | — | — |
| `EntityFormModeEmptyDescriptionToNullRector` | 11.2 | `>=11.2.0` | — | — | — |
| `ErrorCurrentErrorHandlerRector` | 11.3 | config-bound | 13.0.0 | scheduled | change record |
| `FileManagedFileSubmitRector` | 11.3 | config-bound | — | — | — |
| `FileSystemBasenameToNativeRector` | 11.3 | config-bound | 13.0.0 | scheduled | change record |
| `FilterFormatFunctionsToServiceRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `GetDrupalRootToRootPropertyRector` | 11.4 | `>=11.4.0` | — | — | — |
| `GetNameToNameRector` | 11.0 | `>=11.0.0` | — | — | — |
| `GetOriginalClassToGetDecoratedClassesRector` | 11.4 | config-bound | — | — | — |
| `HookRequirementsAlterRenameRector` | 11.3* | `>=11.3.0` | — | — | — |
| `LoadAllIncludesRector` | 11.3 | `>=11.3.0` | — | — | — |
| `LocaleCompareIncToServiceRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `MediaFilterFormatEditFormValidateRector` | 11.4 | config-bound | 12.0.0 | scheduled/observed | change record |
| `MigrateSqlGetMigrationPluginManagerRector` | 11.0 | config-bound | — | — | — |
| `MovePointerToMouseOverRector` | 11.1 | `>=11.1.0` | — | — | — |
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
| `RemoveRootFromConvertDbUrlRector` | 11.3 | config-bound | — | — | — |
| `RemoveRootFromCreateConnectionOptionsFromUrlRector` | 11.2 | `>=11.2.0` | — | — | — |
| `RemoveRouteBuilderDeprecatedArgsRector` | 11.4 | config-bound | — | — | — |
| `RemoveSetUriCallbackRector` | 11.4 | `>=11.4.0` | — | — | — |
| `RemoveSourceModuleFromMigrateSourceAttributeRector` | 11.2* | `>=11.2.0` | — | — | — |
| `RemoveStateCacheSettingRector` | 11.0 | `>=11.0.0` | — | — | — |
| `RemoveToolkitArgFromImageToolkitOperationConstructorRector` | 11.4 | `>=11.4.0` | — | — | — |
| `RemoveTrustDataCallRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `RemoveTwigNodeTransTagArgumentRector` | 11.2 | config-bound | — | — | — |
| `RemoveUpdaterPostInstallMethodsRector` | 11.1 | `>=11.1.0` | 11.1.0 | observed | change record |
| `RemoveViewsRowCacheKeysRector` | 11.4 | `>=11.4.0` | 13.0.0 | scheduled | change record |
| `RenameHookRankingRector` | 11.3* | `>=11.3.0` | 12.0.0 | observed | change record |
| `RenameStopProceduralHookScanRector` | 11.2 | `>=11.2.0` | — | — | — |
| `ReplaceAddCachedDiscoveryMethodCallRector` | 11.1 | config-bound | 11.1.0 | scheduled/observed | change record |
| `ReplaceAlphadecimalToIntNullRector` | 11.2 | config-bound | — | — | — |
| `ReplaceCommentManagerGetCountNewCommentsRector` | 11.3 | config-bound | 12.0.0 | observed | symbol |
| `ReplaceCommentPreviewConstantsRector` | 11.3 | config-bound | — | — | — |
| `ReplaceDateTimeRangeConstantsRector` | 11.2 | config-bound | 12.0.0 | scheduled | change record |
| `ReplaceDialogClassOptionRector` | 11.3 | `>=11.3.0` | — | — | — |
| `ReplaceDrupalStaticResetFileReferencesRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `ReplaceEditorLoadRector` | 11.2 | config-bound | 12.0.0 | observed | symbol |
| `ReplaceEntityOriginalPropertyRector` | 11.2 | config-bound | — | — | — |
| `ReplaceEntityReferenceRecursiveLimitRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `ReplaceExpectDeprecationRector` | 11.4 | config-bound | — | — | — |
| `ReplaceFieldgroupToFieldsetRector` | 11.2 | config-bound | — | — | — |
| `ReplaceHideShowWithPrintedRector` | 11.4 | `>=11.4.0` | 13.0.0 | scheduled | change record |
| `ReplaceItemAttributesWithAttributesRector` | 11.4 | config-bound | — | — | — |
| `ReplaceLocaleBatchProceduralFunctionsRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `ReplaceLocaleConfigBatchFunctionsRector` | 11.1 | config-bound | 12.0.0 | observed/scheduled | change record |
| `ReplaceLocaleTranslationPathConfigRector` | 11.4 | config-bound | — | — | — |
| `ReplaceNodeAccessViewAllNodesRector` | 11.3 | config-bound | 12.0.0 | observed | symbol |
| `ReplaceNodeAddBodyFieldRector` | 11.3 | config-bound | 12.0.0 | observed | symbol |
| `ReplaceNodeModuleProceduralFunctionsRector` | 11.3 | config-bound | 13.0.0 | scheduled | change record |
| `ReplaceNodeSetPreviewModeRector` | 11.3 | config-bound | — | — | — |
| `ReplaceNodeViewControllerRector` | 11.4* | `>=11.4.0` | 12.0.0 | scheduled/observed | change record |
| `ReplaceNonBoolAccessRector` | 11.4 | `>=11.4.0` | — | — | — |
| `ReplacePdoFetchConstantsRector` | 11.2 | config-bound | — | — | — |
| `ReplaceRecipeRunnerInstallModuleRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `ReplaceSessionManagerDeleteRector` | 11.4 | config-bound | 11.4.0 | scheduled | change record |
| `ReplaceSessionWritesWithRequestSessionRector` | 11.2 | config-bound | — | — | — |
| `ReplaceSystemPerformanceGzipKeyRector` | 11.4 | config-bound | — | — | — |
| `ReplaceThemeGetSettingRector` | 11.3 | config-bound | 13.0.0 | scheduled | change record |
| `ReplaceTwigExtensionRector` | 11.3 | config-bound | — | — | — |
| `ReplaceUserOneTimeAuthFunctionsRector` | 11.4 | config-bound | 12.0.0 | scheduled | change record |
| `ReplaceUserSessionNamePropertyRector` | 11.3 | config-bound | — | — | — |
| `ReplaceViewsProceduralFunctionsRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `StatementPrefetchIteratorFetchColumnRector` | 11.2 | config-bound | 12.0.0 | scheduled | change record |
| `StripMigrationDependenciesExpandArgRector` | 11.0 | config-bound | — | — | — |
| `SystemRegionFunctionsRector` | 11.4 | config-bound | 12.0.0 | scheduled | change record |
| `SystemSortThemesRector` | 11.4 | `>=11.4.0` | 12.0.0 | scheduled/observed | change record |
| `TaxonomyTermPageVariableToViewModeRector` | 11.3 | `>=11.3.0` | 13.0.0 | scheduled | change record |
| `UploadedFileConstraintArrayOptionsToNamedArgsRector` | 11.4 | config-bound | — | — | — |
| `UseEntityTypeHasIntegerIdRector` | 11.4 | config-bound | 13.0.0 | scheduled | change record |
| `UserLoadByNameAndMailRector` | 11.4 | `>=11.4.0` | 13.0.0 | scheduled | change record |
| `ViewsBlockItemsPerPageNoneToNullRector` | 11.2 | `>=11.2.0` | — | — | — |
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