# PR #419 review: composer-based sets, and what it means for version bounding

**Date:** 2026-09-20 · **PR:** [palantirnet/drupal-rector#419](https://github.com/palantirnet/drupal-rector/pull/419)
· **Branch:** `TomasVotruba:composer-based-set` · **Status:** open, author asked
for feedback and explicitly said not to merge yet

Companion data: [`rector-rule-version-matrix.md`](rector-rule-version-matrix.md)
— every rule's deprecation and removal version, verified against the code.

## What the PR does

Replaces `DrupalSetProvider` (Rector deprecated `SetProviderInterface` and
`ComposerTriggeredSet`) with a single generated set,
`config/composer-based.php`, exposed as `DrupalSetList::COMPOSER_BASED`. Every
rule is bound to a `drupal/core` version:

- **Configurable rules** get their constraint per configuration entry, inside
  `composer-based.php`, via Rector's
  `RectorConfig::ruleWithConfigurationComposerVersionBound()`.
- **Plain rules** — 76 of them — implement
  `Rector\VersionBonding\Contract\ComposerPackageConstraintInterface` on the
  class and return e.g. `new ComposerPackageConstraint('drupal/core', '>=11.3.0')`.

It also adds three custom PHPStan rules under `utils/PHPStan` so no rule can
escape registration, and **drops the `rector/rector: >=2.6.2` conflict**,
raising the requirement to `^2.6`.

## Resolved during this review

### 1. Third-party sets had to go — done

Rector 2.6.2 deleted the per-version Symfony/PHPUnit/Twig set *files* and the
constants naming them. `SymfonySetList` now carries only `CONFIGS`,
`COMPOSER_BASED`, `SYMFONY_CODE_QUALITY`, `SYMFONY_CONSTRUCTOR_INJECTION`,
`ANNOTATIONS_TO_ATTRIBUTES`; `PHPUnitSetList` likewise. Our D8/D9/D10 configs
referenced `SYMFONY_40`–`64`, `PHPUNIT_60`–`90` and `TWIG_24`, and
`RectorConfig::sets()` does `Assert::fileExists()`, so those sets aborted:

```
[ERROR] Undefined constant Rector\Symfony\Set\SymfonySetList::SYMFONY_64
```

This was the whole content of the 37 PHPStan errors the branch carried, and it
is a consequence of lifting the conflict, not of the set rework.

**Fixed** in `e05e5f96` (pushed to the PR): references dropped rather than
remapped, since upstream offers no per-version replacement. The Drupal sets now
contain Drupal rules only. Dropped with them: the
`AddDoesNotPerformAssertionToNonAssertingTestRector` skip in the D8 set (it only
existed to suppress a rule from the PHPUnit 6 set), the `TwigSetList`
`TWIG_24`/`TWIG_240` detection dance, and 15 stale `SymfonySetList` entries in
the PHPStan baseline. Verified on Rector 2.6.7: PHPStan 37 → 0, 701 tests green,
and the six edited per-minor configs load where the previous revision aborted.

README and CHANGELOG document the migration for anyone who relied on the
bundled sets (`->withComposerBased(phpunit: true, symfony: true, twig: true)`,
with the caveat that it keys off the installed PHPUnit/Symfony/Twig version
rather than the Drupal one).

### 2. The global constraint filter — not a problem after all

`ComposerPackageConstraintFilter` runs unconditionally in
`RectorNodeTraverser::prepareNodeVisitors()`, over every registered rule
regardless of how it was registered. A rule whose `drupal/core` constraint is
not satisfied is dropped silently — including when the user explicitly loads a
`Drupal*SetList` set by hand, and including when `drupal/core` is not installed
at all (`$packageVersion === null` is treated as *not satisfied*).

That looked like it killed the documented "prepare for the next major before
upgrading" workflow. **It does not**, for two reasons established in review:

- **A rule targets the version the deprecation *lands* in, one or two majors
  before removal.** For a 10.x → 11 upgrade you need the rules for deprecations
  removed in 11.0, i.e. the D8/D9/D10 ones. D8 rules bound `>=8.5.0` and D9
  rules bound `>=9.x` are satisfied by any 10.x core, and **no D10 rule is
  globally constrained at all** (the 76 constrained rules are 13 D8 + 23 D9 +
  40 D11). Nothing you need for the upgrade is withheld.
- **Contrib is analysed inside a Drupal installation**, so `getcwd()` is the
  site root — `type: project`, real core in `installed.json`. The
  `InstalledPackageResolver::isLibrary()` path, which pins a non-`project`
  package to the *lowest* version its own constraint allows, never fires.

Also relevant: **none of the 69 BC-wrapping rules** (`AbstractDrupalCoreRector`
subclasses, which emit `DeprecationHelper` wrappers) is globally constrained.
All 76 constrained rules extend plain `AbstractRector` and rewrite
unconditionally to the new API, so withholding them from an older core is the
*correct* behaviour — running them there would emit code that fatals on that
site's own core.

Residual, both minor: the filtering is silent in a normal `rector process` run
(only `vendor/bin/rector composer-based` lists inactive rules), and the test
harness needs a stub `composer.json` claiming core 12.0.0 because drupal-rector's
own repo is not a Drupal site.

**Verdict: no change needed.** The earlier suggestion to move constraints off
the rule classes solves a problem that isn't there for the look-ahead case. The
split the PR already makes — global constraints on the non-BC rules, set-local
constraints on the BC-wrapping ones — lines up with which rules are safe to run
ahead of the installed core.

## Open: should the Drupal 8 / 9 rules stay?

Not resolved. The evidence, so it does not have to be gathered again:

**Test coverage is thin, and it is not neglect — it is history.**

| Major | Rules | With tests | Untested |
|---|---|---|---|
| Drupal8 | 18 | 7 | **11** |
| Drupal9 | 25 | 12 | **13** |
| Drupal10 | 6 | 5 | 1 |
| Drupal11 | 93 | 93 | 0 |
| Drupal12 | 1 | 1 | 0 |

24 of 43 D8/D9 rules have no unit test, and no functional coverage either: the
`rector_examples` workflow that used to exercise them was deleted on 2026-05-01
in `927a3dc3`, *"chore: we now only really use unit tests, only some very old
fixtures remain."*

**Maintenance is sweeps only.** `src/Drupal8` saw 10 commits in two years and
`src/Drupal9` 8, against 113 for `src/Drupal11`. Reading them, all are
mechanical — codestyle, PHPStan 2 / Rector 2, the docs interface, this PR's
constraint bonding — except one real fix, `fix: prevent D9+D11 set collision in
subclassed configurable rectors` (2026-06-01), which is also evidence that the
old rules can still break current ones.

**D8 was already half-dropped** in #311 (2024-10-24): removed from the default
set, PHP 7.4 testing dropped, old fixtures removed. The shipped `rector.php` has
loaded only D10 + D11 ever since — its `Drupal8SetList` / `Drupal9SetList`
imports at lines 10–11 are dead.

**But removal is a real BC break.** 16 contrib projects have a committed
`rector.php` naming these setlists — `decoupled_router`, `civictheme`, `dkan`,
`druxt`, `testmode`, `drupal_helpers`, `coveo`, `link_magician`,
`custom_formatters`, `field_tokens`, `generated_content`, `imagefield_tokens`,
`jsonapi_views`, `oembed_formatter_plus`, `subentity`, `webform_openfisca`,
`views_htmx`, `demo_design_system` — several clearly copy-pasted from one
template. Removing the constants turns those into `Undefined constant` on the
next run.

**One thing this PR changes by accident.** `config/composer-based.php` registers
every rule, D8 and D9 included:

| Major | Registrations in `composer-based.php` |
|---|---|
| Drupal8 | 23 |
| Drupal9 | 28 |
| Drupal10 | 5 |
| Drupal11 | 93 |
| Drupal12 | 1 |

Bound `>=8.5.0` / `>=9.x`, which every D10 and D11 core satisfies. So
`DrupalSetList::COMPOSER_BASED` — the path this PR makes the recommended one —
switches all 43 D8/D9 rules back on for everybody, 24 of them untested. That
silently reverses the 2024 decision to drop them from the default. **Worth
deciding deliberately inside this PR**, independently of the keep/delete
question.

**Options, cheapest first**

1. **Exclude D8/D9 from `composer-based.php`.** One line per rule, breaks
   nobody (explicit setlists still load them), and restores the 2024 default.
   Consistent with the logic that removed them then: a deprecation removed in
   9.0 cannot exist in working D11 code, so these rules are opt-in salvage
   tooling rather than part of "fix what is deprecated on my core".
2. **Triage the 24 untested rules with evidence**, using the `rector-live-test`
   tooling against real D11-compatible contrib. Rules that fire correctly stay;
   rules that misfire get deleted individually. Triage on the rule, not on the
   major.
3. **Bound them** — see below.
4. **Deprecate the setlists now, remove in 2.0**, keeping the constants pointing
   at empty configs for one major so nobody's `rector.php` fatals.

## Open: bounded constraints (`>=8.5.0 <9.0.0`)

The idea: instead of an open-ended lower bound, state the window in which a
deprecation is actually actionable — from the version it landed in, up to the
version the old API was removed. Below the window the old API is not yet
deprecated; at or above it, code still calling it is already fatal.

### The mechanism allows it; only our own rule does not

`ComposerPackageConstraint`'s version string goes straight to
`Semver::satisfies()`, so any Composer constraint is valid. Verified against the
exact prefixed class the filter uses:

```
>=8.5.0                8.5.0=Y  8.9.5=Y  9.4.0=Y  10.3.0=Y  11.4.0=Y
>=8.5.0 <10.0.0        8.4.0=n  8.5.0=Y  9.5.0=Y  10.0.0=n  11.4.0=n
>=8.1 <=9.99           8.0.0=n  8.1.0=Y  9.99.0=Y 10.0.0=n
^8.5 || ^9             8.5.0=Y  9.4.0=Y  10.0.0=n
```

Prereleases behave sanely inside a bounded range, which matters because Drupal
cores install as `-dev`: `9.5.0-beta1` and `9.5.0-dev` both satisfy
`>=8.5.0 <10.0.0`; `10.0.0-beta1` does not. No npm-style "prerelease only
matches its own tuple" surprise.

End-to-end, with `UserLoadByNameAndMailRector` temporarily rebound to
`>=10.0.0 <11.0.0`:

```
core  9.5.0  ->  [OK] Rector is done!            (below range, filtered)
core 10.3.0  ->  * UserLoadByNameAndMailRector   (inside range, applied)
core 11.4.0  ->  [OK] Rector is done!            (above range, filtered)
```

**What blocks it is ours**, added by this PR —
`utils/PHPStan/Rule/BoundRuleConfigurationRule.php`:

```php
public const VERSION_CONSTRAINT_REGEX = '#^>=\d+\.\d+\.\d+$#';
```

`ComposerPackageConstraintRule` enforces that on every rule class ("Bind the
rule to an exact version the deprecation was introduced in, e.g. `>=11.3.0`").
Relax the regex and ranges are available immediately. Worth asking Tomas why one
canonical shape — likely so `rector composer-based` output stays comparable.

### What the evidence supports

From the matrix, after verifying against the code: **38 of 43 D8/D9 rules can be
bounded, every one on an `observed` removal** (the symbol is gone from the tree,
not merely promised to go). The five that cannot target behaviour changes rather
than removed symbols and are correct to stay open-ended.

Drupal 11 is the opposite case: 42 of its 93 rules have no removal version at
all, and of the 51 that do, only 8 are `observed` — the rest are `scheduled` for
12.0.0 or 13.0.0.

### Three rules any implementation must follow

1. **Derive the upper bound from `removal_in`, never from arithmetic on the
   deprecation version.** Three independent proofs:
   `RequestTimeConstRector` sits in the 8.3 set but `REQUEST_TIME` was not
   removed until **11.0.0** — "deprecation major + 1" would have given `<9.0.0`
   and silently disabled it for every D9 and D10 user; CR 3349345 removes inside
   a major (10.2 → 10.5.0); CR 3461934 gives one minor of grace (11.0 → 11.1.0).
2. **Prefer `observed` over `scheduled`.** A scheduled removal is core's current
   promise and has slipped before. Bounding on a promise turns a slipped removal
   into a silently inactive rule. This is why D11 should be left alone for now.
3. **A bound on a generic rule belongs per configuration entry, not on the
   class.** `FunctionToServiceRector`'s 97 entries span removals from 9.0.0 to
   13.0.0; `FunctionToStaticRector`'s 20 and `ConstantToClassConstantRector`'s
   14 likewise. No class-level constraint can be right for them.

### The catch

The constraint filter is global, so a class-level upper bound removes the rule
**even when someone explicitly loads the setlist** — exactly the
inherited-ancient-codebase case those rules exist for, and they would get a
silent zero. If the bound is wanted *and* the manual escape hatch kept, the
constraint has to live in `composer-based.php` rather than on the rule class.
This is the one place where the constraint-placement question, dismissed above
for look-ahead, actually bites.

## Recommended sequence

1. Decide whether D8/D9 belong in `composer-based.php` — smallest change, and
   this PR introduces the regression.
2. Put the bounded-constraint question to Tomas, since the blocking regex is new
   in this PR and the placement trade-off is theirs to weigh.
3. If bounding goes ahead: D8/D9 only, from `removal_in`, `observed` only,
   per configuration entry for the generic rules. Leave D11 until its removals
   land.
4. Triage the 24 untested D8/D9 rules against real contrib before any
   keep/delete decision on the rules themselves.

## Landed on the branch

| Commit | |
|---|---|
| `4c17ca82` | merge `origin/main` |
| `e05e5f96` | drop the bundled Symfony, PHPUnit and Twig sets (pushed to PR #419) |
| `a9c7c53b` | rule version matrix, pass 1 (change records) |
| `feccc2e1` | rule version matrix, pass 2 (verified against the code) |

The two matrix commits are documentation only and have not been pushed to the
PR — they are analysis, not part of the proposal.
