# PR #436 "Skip legacy" — findings and plan

PR: https://github.com/palantirnet/drupal-rector/pull/436 (nlighteneddesign, branch `skip-legacy`, maintainer edits allowed).
Local checkout: branch `pr-436-skip-legacy` (PR head `fbddda97`, based on `3c584968`, i.e. before #419; conflicts with main in `HookConvertRector`).
Status 2026-10-30: analysed, not started. CI on the PR: php-cs-fixer and the functional test fail.

## What the PR does

Adds `$skipLegacy` to `HookConvertRector`. When true, the rule removes the procedural hook instead of leaving a `#[LegacyHook]` wrapper, and doesn't write `<module>.services.yml`. It is set from a `--skip-legacy` CLI flag (`$_SERVER['argv']`), and forced on for `.theme` files and for files under drupal/core (core already skipped legacy before the PR).

## Problems

1. **The flag can't work.** Rector's console rejects unknown options. On a Drupal 11.4 test site, `vendor/bin/rector process --skip-legacy …` fails with `The "--skip-legacy" option does not exist.` before any rule runs.
2. **The theme flag sticks.** `$this->skipLegacy = TRUE` is set on the rule instance in `refactor()` and never reset, so once Rector processes one `.theme` file, every module after it also loses its wrappers.
3. **cs-fixer:** uppercase `TRUE`/`FALSE`, and a blank line at the start of `refactor()`.
4. **Conflicts with main:** the `method_exists($this, 'getFile')` fallback was removed by #419, and the hook-class import filter was added there.

## What "legacy" means here

The two pieces only work together:

- `getLegacyHookFunction()` keeps the procedural function, marked `#[LegacyHook]`, with a body of `\Drupal::service(FooHooks::class)->method(...)`.
- `writeServicesYml()` registers `FooHooks` as an autowired service, so that `\Drupal::service()` call works on Drupal before 11.1, where hook classes aren't discovered as services.

So they are only needed when the code has to keep running on Drupal < 11.1. On 11.1+ core skips `#[LegacyHook]` functions and discovers `src/Hook` classes itself.

## Themes

- Core added OOP hooks for themes in **11.3** (`ThemeHookCollectorPass`, #3544715; not in 11.2.0, present in 11.3.0). Theme hook classes are registered as autowired services automatically, and functions with `#[LegacyHook]` are skipped.
- Themes can't register services, so a theme legacy wrapper (`\Drupal::service(ThemeHooks::class)`) can never work: on < 11.3 the service doesn't exist, and on 11.3+ the wrapper is skipped. The legacy wrapper is never useful for themes.
- Converting a theme without the wrapper removes the procedural hook, which breaks the theme on Drupal < 11.3. So converting a theme is only safe when the theme requires 11.3+.

## Plan (agreed direction: a setting, not a CLI flag)

Use the existing `DrupalRectorSettings` instead of a new option. `HookConvertRector` gets `DrupalRectorSettings` injected (like `AbstractDrupalCoreRector` rules) and decides **per file**, with no instance state carried between files:

| File | Legacy wrapper + services.yml |
|---|---|
| under drupal/core | never (as today) |
| module (`.module`, `.inc`) | only if `isBackwardCompatibilityEnabled()` and `getMinimumCoreVersionSupported()` < 11.1.0 |
| theme (`.theme`) | never |

- With the defaults (BC on, minimum 10.1.0), modules behave exactly as now. Custom site code, which is what the PR author wants, uses `->disableBackwardCompatibility()` or `->setMinimumCoreVersionSupported('11.1.0')`.
- HookConvertRector runs as its own pass with its own config (see the README section on converting hooks), so the composer-based set's forced BC-off singleton doesn't interfere, as long as that pass doesn't load `COMPOSER_BASED`.

**Open decision:** themes when the minimum supported core is < 11.3. Either skip converting `.theme` files (with a note in the README), or convert them and document that the theme then needs 11.3+. Recommendation: skip, since converting silently breaks the theme on older cores.

## To do when resuming

1. Rebase `pr-436-skip-legacy` onto main (or start fresh from main, keeping the author's commit for credit).
2. Implement the table above. Remove `$skipLegacy` and the argv parsing.
3. Fixtures: module with defaults (unchanged), module with BC off, module with minimum 11.1.0, theme (no wrapper, no services.yml; plus the < 11.3 case for whichever option is chosen), and a mixed run (theme then module in one process) to cover the old stickiness bug. Unit tests are in `tests/src/Rector/Convert/HookConvertRector/`; the functional test is `tests/functional/hookconvertrector` (only has a module today).
4. README "Converting hooks to OOP hook classes" section, and a CHANGELOG entry (Changed).
5. Push to the author's branch (maintainer edits allowed) or open a follow-up PR crediting them.
