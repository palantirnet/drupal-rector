# Rule version matrix: deprecation in, removal out

**Generated:** 2026-09-20 · **Branch:** `pr-419-composer-based-set` · **Status:** pass 1 of 2

Every drupal-rector rule with the Drupal version its deprecation landed in and
the version the old API was actually removed in. Built to answer one question:
can a rule carry a *bounded* composer constraint (`>=8.5.0 <9.0.0`) instead of
the open-ended `>=8.5.0` that PR #419 gives it?

Change records are the primary source, as asked. Where the change record says
nothing, the core symbol catalog's observed removal fills the gap — and where
the two disagree, the catalog wins, because a change record describes intent
and the catalog describes the tree.

## Method

1. **Rule → introduction version** from which per-minor config registers it
   (`config/drupal-11/drupal-11.4-deprecations.php` ⇒ 11.4). Authoritative:
   it is what drupal-rector itself asserts, and it drives the sets.
2. **Rule → change records** by scraping `drupal.org/node/<nid>` links from the
   rule class *and* from the config line that registers it — the generic
   configurable rules (`FunctionToServiceRector` and friends) carry their CR as
   a comment next to each configuration entry, not on the class.
3. **CR → declared target** from `change_record.change_to_branch`.
4. **CR → removed API → removal version** via `change_record_symbol`
   (`role = 'from'`) joined to `core_symbol.removal_in` / `removal_kind`.

`removal_kind` is the important column:

- **`observed`** — the symbol is gone from the tree. This is the code-level
  verification, already done by the indexer.
- **`scheduled`** — still present, carrying an `@deprecated` promise. A
  statement of intent that core can still break.

Data: `api.tresbien.tech`, `core_symbol` and `change_record` stamped
2026-09-19, `core_symbol_evidence` 2026-09-16.

## Summary

| Major | Rules | With a CR | With removal data | Code-observed |
|---|---|---|---|---|
| Drupal8 | 18 | 15 | 10 | 10 |
| Drupal9 | 25 | 8 | 6 | 6 |
| Drupal10 | 6 | 4 | 4 | 2 |
| Drupal11 | 93 | 88 | 50 | 15 |
| Drupal12 | 1 | 0 | 0 | 0 |
| generic | 11 | 6 | 6 | 6 |

Removal versions across all rules:

| Removed in | Rules | | Removed in | Rules |
|---|---|---|---|---|
| 9.0.0 | 11 | | 11.3.0 | 1 |
| 10.0.0 | 7 | | 11.4.0 | 1 |
| 10.5.0 | 1 | | 12.0.0 | 31 |
| 11.0.0 | 6 | | 13.0.0 | 36 |
| 11.1.0 | 2 | | | |
| 11.2.0 | 1 | | | |

The shape is what the deprecation policy predicts: D8 rules → removed in 9.0.0,
D9 rules → 10.0.0, D10 rules → 11.0.0, D11 rules → 12.0.0 or 13.0.0. Every
removal at or below 11.0.0 is `observed`; almost everything at 12.0.0/13.0.0 is
`scheduled`, which is exactly the line between "verified in the tree" and
"core's current promise".

## Where the change records are wrong or missing

You were right to want the code checked. Six records disagree with the tree or
can't be read at all:

| CR | Declared | Removal found | Evidence | Problem |
|---|---|---|---|---|
| [3519187](https://www.drupal.org/node/3519187) | 11.3.x | 11.3.0 | scheduled | Removal version equals the deprecation version |
| [3527501](https://www.drupal.org/node/3527501) | 11.2.x | 11.2.0 | observed | Same — and the symbol is already gone |
| [3570851](https://www.drupal.org/node/3570851) | 11.4.x | 11.4.0 | scheduled | Same |
| [3442229](https://www.drupal.org/node/3442229) | 11.1.x | 11.1.0 + 12.0.0 | mixed | One symbol removed in its own deprecation minor, another in 12.0.0 |
| [3349345](https://www.drupal.org/node/3349345) | 10.2.x | 10.5.0 | scheduled | Removal inside the same major — breaks the "next major" assumption outright |
| [3461934](https://www.drupal.org/node/3461934) | 10.4.x, 11.0.x | 11.1.0 | observed | Deprecated in 11.0, gone by 11.1 — one minor of grace |
| [3513877](https://www.drupal.org/node/3513877) | `11.3,x` | — | — | Typo in the record's own branch field (comma for a dot) |

3349345 and 3461934 are the ones that matter for the bounding idea: a removal
that lands *inside* a major means an upper bound derived from "deprecation
major + 1" would be wrong, and would silently switch a rule off a minor too
late. Any generated bound has to come from `removal_in`, never from arithmetic
on the deprecation version.

### Coverage gaps

- **130 of 275 change records referenced by our rules are not in the index** —
  they resolve to no `change_record` row. Mostly pre-9 records (`455724`,
  `1019966`, `1452100`) plus newer ones the index has not reviewed/published.
  For those rules, the introduction version is only as good as our own config
  placement.
- **26 rules cite no change record at all**, and a further 7 cite only
  unindexed ones. All of the D9 `AssertLegacyTrait`
  family (`AssertFieldByIdRector`, `PassRector`, `GetRawContentRector`, …), plus
  `RequestTimeConstRector`, `StaticToFunctionRector`, `DrupalServiceRenameRector`,
  `FileUrlTransformRelativeRector`, `FromUriRector`, the taxonomy trio, and the
  three infrastructure rules (`HookConvertRector`, `DeprecationHelperRemoveRector`,
  `AnnotationToAttributeRector`) which legitimately have none.
- **45 rules have a CR but no removal data** — the record links no `from`
  symbol, so the catalog has nothing to check. Common for records about
  behaviour rather than a named API.

Net: of 154 rules, **121 cite an indexed change record**, **76 have a removal
version**, and only **39** of those are code-observed. A blanket
bounded-constraint migration cannot be generated from this data alone; it is
solid for D8/D9 and thin for D11.

## Drupal 8 / 9: the bounding candidates

These are the 43 rules the `>=8.1 <=9.99` idea is aimed at. Every one that has
removal evidence is `observed` — the API is gone from the tree, not merely
promised to go.

`>=X <Y` is the constraint the evidence supports; rules with no removal
evidence get no bound until pass 2 establishes one.

| Rule | Set | Current constraint | Removed in | Evidence-backed bound |
|---|---|---|---|---|
| `AssertFieldByIdRector` | 9.1 | `>=9.1.0` | — | — no removal evidence |
| `AssertFieldByNameRector` | 9.1 | `>=9.1.0` | — | — no removal evidence |
| `AssertLegacyTraitRector` | 9.1 | config-bound | — | — no removal evidence |
| `AssertNoFieldByIdRector` | 9.1 | `>=9.1.0` | — | — no removal evidence |
| `AssertNoFieldByNameRector` | 9.1 | `>=9.1.0` | — | — no removal evidence |
| `AssertNoUniqueTextRector` | 9.1 | `>=9.1.0` | — | — no removal evidence |
| `AssertOptionSelectedRector` | 9.1 | `>=9.1.0` | — | — no removal evidence |
| `ConstructFieldXpathRector` | 9.1 | `>=9.1.0` | — | — no removal evidence |
| `DBRector` | 8.0 | config-bound | 9.0.0 | `>=8.0.0 <9.0.0` |
| `DrupalLRector` | 8.0 | `>=8.0.0` | — | — no removal evidence |
| `DrupalServiceRenameRector` | 8.8 | config-bound | — | — no removal evidence |
| `DrupalSetMessageRector` | 8.5 | `>=8.5.0` | 9.0.0 | `>=8.5.0 <9.0.0` |
| `DrupalURLRector` | 8.0 | `>=8.0.0` | — | — no removal evidence |
| `EntityCreateRector` | 8.0 | `>=8.0.0` | 9.0.0 | `>=8.0.0 <9.0.0` |
| `EntityDeleteMultipleRector` | 8.0 | `>=8.0.0` | 9.0.0 | `>=8.0.0 <9.0.0` |
| `EntityInterfaceLinkRector` | 8.0 | `>=8.0.0` | 9.0.0 | `>=8.0.0 <9.0.0` |
| `EntityLoadRector` | 8.0 | config-bound | 9.0.0 | `>=8.0.0 <9.0.0` |
| `EntityManagerRector` | 8.0 | `>=8.0.0` | 9.0.0 | `>=8.0.0 <9.0.0` |
| `EntityViewRector` | 8.0 | `>=8.0.0` | — | — no removal evidence |
| `ExtensionPathRector` | 9.3 | config-bound | 10.0.0 | `>=9.3.0 <10.0.0` |
| `FileBuildUriRector` | 9.3 | `>=9.3.0` | 10.0.0 | `>=9.3.0 <10.0.0` |
| `FileCreateUrlRector` | 9.3 | `>=9.3.0` | 10.0.0 | `>=9.3.0 <10.0.0` |
| `FileDefaultSchemeRector` | 8.8 | `>=8.8.0` | 9.0.0 | `>=8.8.0 <9.0.0` |
| `FileUrlTransformRelativeRector` | 9.3 | `>=9.3.0` | — | — no removal evidence |
| `FromUriRector` | 9.3 | `>=9.3.0` | — | — no removal evidence |
| `FunctionToFirstArgMethodRector` | 9.3 11.2 11.3 | config-bound | 12.0.0 | — spans several minors |
| `FunctionalTestDefaultThemePropertyRector` | 8.8 | `>=8.8.0` | — | — no removal evidence |
| `GetAllOptionsRector` | 9.1 | `>=9.1.0` | — | — no removal evidence |
| `GetMockRector` | 8.4 | config-bound | — | — no removal evidence |
| `GetRawContentRector` | 9.1 | `>=9.1.0` | — | — no removal evidence |
| `LinkGeneratorTraitLRector` | 8.0 | `>=8.0.0` | 9.0.0 | `>=8.0.0 <9.0.0` |
| `ModuleLoadRector` | 9.4 | `>=9.4.0` | 10.0.0 | `>=9.4.0 <10.0.0` |
| `PassRector` | 9.1 | `>=9.1.0` | — | — no removal evidence |
| `ProtectedStaticModulesPropertyRector` | 9.0 | `>=9.0.0` | — | — no removal evidence |
| `RequestTimeConstRector` | 8.3 | `>=8.3.0` | — | — no removal evidence |
| `SafeMarkupFormatRector` | 8.0 | `>=8.0.0` | 9.0.0 | `>=8.0.0 <9.0.0` |
| `StaticToFunctionRector` | 8.6 | config-bound | — | — no removal evidence |
| `SystemSortByInfoNameRector` | 9.3 | `>=9.3.0` | 10.0.0 | `>=9.3.0 <10.0.0` |
| `TaxonomyTermLoadMultipleByNameRector` | 9.3 | `>=9.3.0` | — | — no removal evidence |
| `TaxonomyVocabularyGetNamesDrupalStaticResetRector` | 9.3 | `>=9.3.0` | — | — no removal evidence |
| `TaxonomyVocabularyGetNamesRector` | 9.3 | `>=9.3.0` | — | — no removal evidence |
| `UiHelperTraitDrupalPostFormRector` | 9.1 | `>=9.1.0` | — | — no removal evidence |
| `UserPasswordRector` | 9.1 | `>=9.1.0` | — | — no removal evidence |

**15 of 43 can be bounded from this evidence today. 27 cannot** — no removal
version, because no change record is cited (26) or the cited one links no
removed symbol (`EntityViewRector`). The remaining one,
`FunctionToFirstArgMethodRector`, is a generic rule registered in 9.3, 11.2 *and*
11.3, so no single class-level bound can be right for it; its versions belong
per configuration entry, the way `ruleWithConfigurationComposerVersionBound()`
already handles the other configurable rules.

`config-bound` in the table means the rule takes configuration and so was never
given a class constraint by PR #419 — its version lives in
`config/composer-based.php` instead, and it is unaffected by the global filter.

The 9.1 `AssertLegacyTrait` block is the single biggest gap: 8 rules, no CRs, no
removal data. They target `Drupal\FunctionalTests\AssertLegacyTrait`, which is
worth one `lookup_core_symbol` call in pass 2 to settle the whole group at once.

## Pass 2: verify against the tree

This pass took change records first, as asked, and the catalog only filled
gaps. Pass 2 should invert that and check the code directly, because the six
records above prove the records are not reliable on their own.

Three jobs, cheapest first:

1. **Settle the 9.1 `AssertLegacyTrait` block in one call** —
   `lookup_core_symbol` on `\Drupal\FunctionalTests\AssertLegacyTrait` and
   `\Drupal\KernelTests\AssertLegacyTrait` covers 9 rules at once
   (`AssertFieldByIdRector`, `AssertFieldByNameRector`, `AssertLegacyTraitRector`,
   `AssertNoFieldByIdRector`, `AssertNoFieldByNameRector`, `AssertNoUniqueTextRector`,
   `AssertOptionSelectedRector`, `ConstructFieldXpathRector`, `GetAllOptionsRector`,
   `PassRector`, `GetRawContentRector`, `UiHelperTraitDrupalPostFormRector`).
2. **Resolve the other 17 D8/D9 rules with no CR** by extracting the symbol each
   one matches from the rule body (`isName($node, 'x')`, `ObjectType('…')`) and
   looking it up directly. The taxonomy trio, `FromUriRector`,
   `FileUrlTransformRelativeRector`, `RequestTimeConstRector`,
   `StaticToFunctionRector` and `DrupalServiceRenameRector` are all named after
   the symbol they replace, so extraction should be reliable.
3. **Re-check the 4 same-version removals** (3519187, 3527501, 3570851,
   3442229) against `git log` in `repos/drupal-core`. Either the catalog's
   `removal_in` is mis-parsed from an `@deprecated` line, or core really did
   remove inside a minor — and if it did, those are BC breaks worth their own
   issue.

Only then is a bounded-constraint change safe to generate mechanically. Two
rules to carry into it, both learned here:

- **Derive the upper bound from `removal_in`, never from the deprecation
  version.** 3349345 (10.2 → 10.5.0) and 3461934 (11.0 → 11.1.0) both remove
  inside a major; "deprecation major + 1" would be wrong for them.
- **Prefer `observed` over `scheduled`.** A `scheduled` removal is core's
  current promise and has slipped before; bounding a rule on a promise turns a
  slipped removal into a silently inactive rule.

## Full matrix

`Set` is the per-minor config that registers the rule — drupal-rector's own
claim about when the deprecation landed. `CR target` is what the change record
says. Where they disagree, the config is what actually drives the sets today.

| Rule | Set | Class constraint | CR target | Removed in | Evidence | CRs |
|---|---|---|---|---|---|---|
| `DBRector` | 8.0 | — | 8.0.x | 9.0.0 | observed | 1 |
| `DrupalLRector` | 8.0 | >=8.0.0 | 8.x | — | — | 1 |
| `DrupalServiceRenameRector` | 8.8 | — | — | — | — | 0 |
| `DrupalSetMessageRector` | 8.5 | >=8.5.0 | 8.5.x | 9.0.0 | observed | 1 |
| `DrupalURLRector` | 8.0 | >=8.0.0 | 8.x | — | — | 1 |
| `EntityCreateRector` | 8.0 | >=8.0.0 | 8.x | 9.0.0 | observed | 1 |
| `EntityDeleteMultipleRector` | 8.0 | >=8.0.0 | 8.x | 9.0.0 | observed | 1 |
| `EntityInterfaceLinkRector` | 8.0 | >=8.0.0 | 8.0.x | 9.0.0 | observed | 1 |
| `EntityLoadRector` | 8.0 | — | 8.x | 9.0.0 | observed | 1 |
| `EntityManagerRector` | 8.0 | >=8.0.0 | 8.0.x | 9.0.0 | observed | 1 |
| `EntityViewRector` | 8.0 | >=8.0.0 | 8.8.x | — | — | 1 |
| `FileDefaultSchemeRector` | 8.8 | >=8.8.0 | 8.8.x | 9.0.0 | observed | 1 |
| `FunctionalTestDefaultThemePropertyRector` | 8.8 | >=8.8.0 | 8.8.x | — | — | 1 |
| `GetMockRector` | 8.4 | — | 8.4.x | — | — | 1 |
| `LinkGeneratorTraitLRector` | 8.0 | >=8.0.0 | 8.0.x | 9.0.0 | observed | 1 |
| `RequestTimeConstRector` | 8.3 | >=8.3.0 | — | — | — | 0 |
| `SafeMarkupFormatRector` | 8.0 | >=8.0.0 | 8.0.x | 9.0.0 | observed | 1 |
| `StaticToFunctionRector` | 8.6 | — | — | — | — | 0 |
| `AssertFieldByIdRector` | 9.1 | >=9.1.0 | — | — | — | 0 |
| `AssertFieldByNameRector` | 9.1 | >=9.1.0 | — | — | — | 0 |
| `AssertLegacyTraitRector` | 9.1 | — | — | — | — | 0 |
| `AssertNoFieldByIdRector` | 9.1 | >=9.1.0 | — | — | — | 0 |
| `AssertNoFieldByNameRector` | 9.1 | >=9.1.0 | — | — | — | 0 |
| `AssertNoUniqueTextRector` | 9.1 | >=9.1.0 | — | — | — | 0 |
| `AssertOptionSelectedRector` | 9.1 | >=9.1.0 | — | — | — | 0 |
| `ConstructFieldXpathRector` | 9.1 | >=9.1.0 | — | — | — | 0 |
| `ExtensionPathRector` | 9.3 | — | 9.3.x | 10.0.0 | observed | 1 |
| `FileBuildUriRector` | 9.3 | >=9.3.0 | 9.3.x | 10.0.0 | observed | 1 |
| `FileCreateUrlRector` | 9.3 | >=9.3.0 | 9.3.x | 10.0.0 | observed | 1 |
| `FileUrlTransformRelativeRector` | 9.3 | >=9.3.0 | — | — | — | 0 |
| `FromUriRector` | 9.3 | >=9.3.0 | — | — | — | 0 |
| `FunctionToFirstArgMethodRector` | 9.3 11.2 11.3 | — | 11.2.x 11.3.x | 12.0.0 | observed | 2 (+1 unindexed) |
| `GetAllOptionsRector` | 9.1 | >=9.1.0 | — | — | — | 0 |
| `GetRawContentRector` | 9.1 | >=9.1.0 | — | — | — | 0 |
| `ModuleLoadRector` | 9.4 | >=9.4.0 | 9.4.x | 10.0.0 | observed | 1 |
| `PassRector` | 9.1 | >=9.1.0 | 9.1.x | — | — | 1 |
| `ProtectedStaticModulesPropertyRector` | 9.0 | >=9.0.0 | 9.0.x | — | — | 1 |
| `SystemSortByInfoNameRector` | 9.3 | >=9.3.0 | 9.3.x | 10.0.0 | observed | 1 |
| `TaxonomyTermLoadMultipleByNameRector` | 9.3 | >=9.3.0 | — | — | — | 0 |
| `TaxonomyVocabularyGetNamesDrupalStaticResetRector` | 9.3 | >=9.3.0 | — | — | — | 0 |
| `TaxonomyVocabularyGetNamesRector` | 9.3 | >=9.3.0 | — | — | — | 0 |
| `UiHelperTraitDrupalPostFormRector` | 9.1 | >=9.1.0 | — | — | — | 0 |
| `UserPasswordRector` | 9.1 | >=9.1.0 | — | — | — | 0 |
| `AnnotationToAttributeRector` | — | — | — | — | — | 0 |
| `ReplaceModuleHandlerGetNameRector` | 10.3 | — | 10.3.x | 12.0.0 | scheduled | 1 |
| `ReplaceRebuildThemeDataRector` | 10.3 | — | 10.3.x | 12.0.0 | scheduled | 1 |
| `ReplaceRequestTimeConstantRector` | 11.0 | — | — | — | — | 0 (+1 unindexed) |
| `SystemTimeZonesRector` | 10.1 | — | 10.1.x | 11.0.0 | observed | 1 |
| `WatchdogExceptionRector` | 10.1 | — | 10.1.x | 11.0.0 | observed | 1 |
| `BlockContentSelectionExtendsRector` | 11.4* | >=11.4.0 | 11.3.x | — | — | 1 (+1 unindexed) |
| `BlockContentTestBaseStringToArrayRector` | 11.1 | >=11.1.0 | 11.1.x | — | — | 1 (+1 unindexed) |
| `CheckMarkupToProcessedTextRector` | 11.4 | >=11.4.0 | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `CommentLinkBuilderConstructorRector` | 11.3 | — | 11.3.x | — | — | 1 (+1 unindexed) |
| `DeprecatedFilterFunctionsRector` | 11.4 | — | 11.4.x | 12.0.0 13.0.0 | scheduled/observed | 1 (+1 unindexed) |
| `DrupalGetHeadersAssocArrayRector` | 11.1 | >=11.1.0 | 11.1.x | — | — | 2 (+1 unindexed) |
| `EntityFormModeEmptyDescriptionToNullRector` | 11.2 | >=11.2.0 | 11.2.x | — | — | 1 (+1 unindexed) |
| `ErrorCurrentErrorHandlerRector` | 11.3 | — | 11.3.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `FileManagedFileSubmitRector` | 11.3 | — | 11.3.x | — | — | 1 (+1 unindexed) |
| `FileSystemBasenameToNativeRector` | 11.3 | — | 11.3.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `FilterFormatFunctionsToServiceRector` | 11.4 | — | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `GetDrupalRootToRootPropertyRector` | 11.4 | >=11.4.0 | 11.4.x | — | — | 1 (+1 unindexed) |
| `GetNameToNameRector` | 11.0 | >=11.0.0 | — | — | — | 0 (+1 unindexed) |
| `GetOriginalClassToGetDecoratedClassesRector` | 11.4 | — | 11.3.x | — | — | 1 (+1 unindexed) |
| `HookRequirementsAlterRenameRector` | 11.3* | >=11.3.0 | 11.3.x | — | — | 1 (+1 unindexed) |
| `LoadAllIncludesRector` | 11.3 | >=11.3.0 | 11.3.x | — | — | 1 (+1 unindexed) |
| `LocaleCompareIncToServiceRector` | 11.4 | — | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `MediaFilterFormatEditFormValidateRector` | 11.4 | — | 11.4.x | 12.0.0 13.0.0 | scheduled/observed | 1 (+1 unindexed) |
| `MigrateSqlGetMigrationPluginManagerRector` | 11.0 | — | 10.1.x | — | — | 1 (+1 unindexed) |
| `MovePointerToMouseOverRector` | 11.1 | >=11.1.0 | 11.4.x | — | — | 1 (+1 unindexed) |
| `NodeAccessRebuildFunctionsRector` | 11.4 | — | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `NodeStorageDeprecatedMethodsRector` | 11.3 | >=11.3.0 | 11.3.x | 11.3.0 | scheduled | 1 (+1 unindexed) |
| `PluginBaseIsConfigurableRector` | 11.1 | — | 10.1.x | 11.0.0 | observed | 1 (+2 unindexed) |
| `RemoveAutomatedCronSubmitHandlerRector` | 11.4 | >=11.4.0 | 11.4.x | 12.0.0 13.0.0 | scheduled/observed | 1 (+1 unindexed) |
| `RemoveCacheExpireOverrideRector` | 11.4 | >=11.4.0 | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `RemoveCacheTagChecksumAssertionsRector` | 11.2 | >=11.2.0 | 11.2.x | 12.0.0 | observed | 2 (+3 unindexed) |
| `RemoveConfigSaveTrustedDataArgRector` | 11.4 | — | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `RemoveDrupalToStringTraitRector` | 11.4 | >=11.4.0 | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `RemoveFilterTipsLongParamRector` | 11.4 11.4* | >=11.4.0 | 11.4.x | — | — | 1 (+1 unindexed) |
| `RemoveHandlerBaseDefineExtraOptionsRector` | 11.2 | >=11.2.0 | 11.2.x | 12.0.0 | scheduled | 1 (+1 unindexed) |
| `RemoveInstallSchemaSystemSequencesRector` | 11.4 | >=11.4.0 | 10.2.x | 10.5.0 | scheduled | 1 (+1 unindexed) |
| `RemoveLinkWidgetValidateTitleElementRector` | 11.4 | >=11.4.0 | 11.4.x | 12.0.0 | scheduled | 1 (+1 unindexed) |
| `RemoveModuleHandlerAddModuleCallsRector` | 11.2 | >=11.2.0 | 11.2.x 11.3.x | 12.0.0 | scheduled | 2 (+1 unindexed) |
| `RemoveModuleHandlerDeprecatedMethodsRector` | 11.1 | >=11.1.0 | 11.1.x | 12.0.0 | scheduled | 1 (+2 unindexed) |
| `RemovePhpUnitCompatibilityTraitRector` | 11.4 | — | — | — | — | 0 (+1 unindexed) |
| `RemoveRendererAddCacheableDependencyNonObjectRector` | 11.3 | >=11.3.0 | 11.3.x 11.2.x | 11.2.0 12.0.0 | scheduled/observed | 3 (+3 unindexed) |
| `RemoveRootFromConvertDbUrlRector` | 11.3 | — | 11.2.x | — | — | 1 (+1 unindexed) |
| `RemoveRootFromCreateConnectionOptionsFromUrlRector` | 11.2 | >=11.2.0 | 11.2.x | — | — | 1 (+1 unindexed) |
| `RemoveRouteBuilderDeprecatedArgsRector` | 11.4 | — | 11.4.x | — | — | 1 (+1 unindexed) |
| `RemoveSetUriCallbackRector` | 11.4 | >=11.4.0 | 11.4.x | — | — | 1 (+1 unindexed) |
| `RemoveSourceModuleFromMigrateSourceAttributeRector` | 11.2* | >=11.2.0 | 11.2.x | — | — | 1 (+1 unindexed) |
| `RemoveStateCacheSettingRector` | 11.0 | >=11.0.0 | — | — | — | 0 (+2 unindexed) |
| `RemoveToolkitArgFromImageToolkitOperationConstructorRector` | 11.4 | >=11.4.0 | 11.4.x | — | — | 1 (+1 unindexed) |
| `RemoveTrustDataCallRector` | 11.4 | — | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `RemoveTwigNodeTransTagArgumentRector` | 11.2 | — | — | — | — | 0 (+2 unindexed) |
| `RemoveUpdaterPostInstallMethodsRector` | 11.1 | >=11.1.0 | 10.4.x, 11.0.x | 11.1.0 | observed | 1 (+1 unindexed) |
| `RemoveViewsRowCacheKeysRector` | 11.4 | >=11.4.0 | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `RenameHookRankingRector` | 11.3* | >=11.3.0 | 11.3.x | 12.0.0 | observed | 1 (+1 unindexed) |
| `RenameStopProceduralHookScanRector` | 11.2 | >=11.2.0 | — | — | — | 0 (+1 unindexed) |
| `ReplaceAddCachedDiscoveryMethodCallRector` | 11.1 | — | 11.1.x | 11.1.0 12.0.0 | scheduled/observed | 1 (+1 unindexed) |
| `ReplaceAlphadecimalToIntNullRector` | 11.2 | — | 11.2.x | — | — | 1 (+1 unindexed) |
| `ReplaceCommentManagerGetCountNewCommentsRector` | 11.3 | — | 11.3.x | 12.0.0 | scheduled | 1 (+1 unindexed) |
| `ReplaceCommentPreviewConstantsRector` | 11.3 | — | 11.3.x | — | — | 1 (+1 unindexed) |
| `ReplaceDateTimeRangeConstantsRector` | 11.2 | — | 11.2.x | 12.0.0 | scheduled | 1 (+1 unindexed) |
| `ReplaceDialogClassOptionRector` | 11.3 | >=11.3.0 | 10.3.x | — | — | 1 (+1 unindexed) |
| `ReplaceDrupalStaticResetFileReferencesRector` | 11.4 | — | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `ReplaceEditorLoadRector` | 11.2 | — | 11.2.x | 12.0.0 | observed | 1 (+1 unindexed) |
| `ReplaceEntityOriginalPropertyRector` | 11.2 | — | 11.2.x | — | — | 1 (+1 unindexed) |
| `ReplaceEntityReferenceRecursiveLimitRector` | 11.4 | — | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `ReplaceExpectDeprecationRector` | 11.4 | — | 11.4.x | — | — | 1 (+1 unindexed) |
| `ReplaceFieldgroupToFieldsetRector` | 11.2 | — | 11.2.x | — | — | 1 (+1 unindexed) |
| `ReplaceHideShowWithPrintedRector` | 11.4 | >=11.4.0 | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `ReplaceItemAttributesWithAttributesRector` | 11.4 | — | 11.4.x | — | — | 1 (+1 unindexed) |
| `ReplaceLocaleBatchProceduralFunctionsRector` | 11.4 | — | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `ReplaceLocaleConfigBatchFunctionsRector` | 11.1 | — | 11.1.x | 12.0.0 13.0.0 | observed/scheduled | 1 (+1 unindexed) |
| `ReplaceLocaleTranslationPathConfigRector` | 11.4 | — | 11.4.x | — | — | 1 (+1 unindexed) |
| `ReplaceNodeAccessViewAllNodesRector` | 11.3 | — | 11.3.x | 12.0.0 | observed | 1 (+1 unindexed) |
| `ReplaceNodeAddBodyFieldRector` | 11.3 | — | 11.3.x | 12.0.0 | observed | 1 (+1 unindexed) |
| `ReplaceNodeModuleProceduralFunctionsRector` | 11.3 | — | 11.3.x | 13.0.0 | scheduled | 1 |
| `ReplaceNodeSetPreviewModeRector` | 11.3 | — | 11.3.x | — | — | 1 (+1 unindexed) |
| `ReplaceNodeViewControllerRector` | 11.4* | >=11.4.0 | 11.4.x | 12.0.0 13.0.0 | scheduled/observed | 2 (+4 unindexed) |
| `ReplaceNonBoolAccessRector` | 11.4 | >=11.4.0 | 11.4.x | — | — | 1 (+1 unindexed) |
| `ReplacePdoFetchConstantsRector` | 11.2 | — | 11.2.x | — | — | 1 (+1 unindexed) |
| `ReplaceRecipeRunnerInstallModuleRector` | 11.4 | — | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `ReplaceSessionManagerDeleteRector` | 11.4 | — | 11.4.x | 11.4.0 | scheduled | 1 |
| `ReplaceSessionWritesWithRequestSessionRector` | 11.2 | — | 11.2.x | — | — | 1 (+1 unindexed) |
| `ReplaceSystemPerformanceGzipKeyRector` | 11.4 | — | 11.4.x | — | — | 1 (+1 unindexed) |
| `ReplaceThemeGetSettingRector` | 11.3 | — | 11.3.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `ReplaceTwigExtensionRector` | 11.3 | — | 11.3.x | — | — | 1 (+2 unindexed) |
| `ReplaceUserOneTimeAuthFunctionsRector` | 11.4 | — | 11.4.x | 12.0.0 13.0.0 | scheduled | 1 (+1 unindexed) |
| `ReplaceUserSessionNamePropertyRector` | 11.3 | — | 11.3,x | — | — | 1 (+1 unindexed) |
| `ReplaceViewsProceduralFunctionsRector` | 11.4 | — | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `StatementPrefetchIteratorFetchColumnRector` | 11.2 | — | 11.2.x | 12.0.0 | scheduled | 1 (+1 unindexed) |
| `StripMigrationDependenciesExpandArgRector` | 11.0 | — | 11.0.x | — | — | 1 (+1 unindexed) |
| `SystemRegionFunctionsRector` | 11.4 | — | 11.4.x | 12.0.0 13.0.0 | scheduled | 1 (+1 unindexed) |
| `SystemSortThemesRector` | 11.4 | >=11.4.0 | 11.4.x | 12.0.0 13.0.0 | scheduled/observed | 1 (+1 unindexed) |
| `TaxonomyTermPageVariableToViewModeRector` | 11.3 | >=11.3.0 | 11.3.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `UploadedFileConstraintArrayOptionsToNamedArgsRector` | 11.4 | — | 11.4.x | — | — | 1 (+1 unindexed) |
| `UseEntityTypeHasIntegerIdRector` | 11.4 | — | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `UserLoadByNameAndMailRector` | 11.4 | >=11.4.0 | 11.4.x | 13.0.0 | scheduled | 1 |
| `ViewsBlockItemsPerPageNoneToNullRector` | 11.2 | >=11.2.0 | 11.2.x | — | — | 1 (+1 unindexed) |
| `ViewsConfigUpdaterClassResolverToServiceRector` | 11.3 | — | 11.3.x | — | — | 1 (+1 unindexed) |
| `ViewsPluginHandlerManagerRector` | 11.4 | — | 11.4.x | 13.0.0 | scheduled | 1 (+1 unindexed) |
| `AddSymfonyConstraintValidatorTypeDeclarationsRector` | 11.0 12.0 | >=11.0.0 | — | — | — | 0 |
| `ClassConstantToClassConstantRector` | 9.1 10.3 11.2 11.4 | — | 10.3.x 11.2.x 11.4.x 9.1.x | 12.0.0 13.0.0 | scheduled/observed | 4 (+2 unindexed) |
| `ConstantToClassConstantRector` | 8.5 8.7 9.3 11.2 11.3 11.4 | — | 11.2.x 11.3.x 11.4.x 8.5.x 8.7.x 9.3.x | 9.0.0 10.0.0 12.0.0 13.0.0 | observed/scheduled | 6 (+1 unindexed) |
| `DeprecationHelperRemoveRector` | — | — | — | — | — | 0 |
| `FunctionCallRemovalRector` | 11.2 11.3 11.4 | — | 11.2.x 11.3.x 11.4.x | 12.0.0 13.0.0 | observed/scheduled | 4 (+3 unindexed) |
| `FunctionToServiceRector` | 8.0 8.7 8.8 9.3 10.2 11.2 11.3 11.4 | — | 10.2.x 11.2.x 11.3.x 11.4.x 9.3.x | 10.0.0 11.0.0 12.0.0 13.0.0 | observed/scheduled | 10 (+17 unindexed) |
| `FunctionToStaticRector` | 8.2 9.3 10.1 10.2 10.3 11.1 11.2 11.3 11.4 | — | 10.1.x 10.2.x 10.3.x 11.1.x 11.2.x 11.3.x 11.4.x 8.0.x | 11.0.0 12.0.0 13.0.0 | observed/scheduled | 8 (+7 unindexed) |
| `HookConvertRector` | — | — | — | — | — | 0 |
| `MethodToMethodWithCheckRector` | 8.0 8.8 9.2 10.2 10.3 11.1 11.2 | — | 10.2.x 10.3.x 11.2.x | 11.0.0 12.0.0 | observed/scheduled | 3 |
| `PhpUnitAddRunTestsInSeparateProcessesAttributeRector` | 11.4 | — | — | — | — | 0 |
| `PhpUnitTestAnnotationToAttributeRector` | 11.0 | — | — | — | — | 0 (+1 unindexed) |
| `ShouldCallParentMethodsRector` | 9.0 10.0 | >=9.0.0 | — | — | — | 0 |