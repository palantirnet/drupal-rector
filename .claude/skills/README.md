# drupal-rector Claude Code Skills

Claude Code skills for the drupal-rector development workflow. Each skill is a structured prompt that guides Claude through a specific task — invoke them with `/skill-name` in Claude Code.

## Skills

### `/rector-discover`

Lists unimplemented rules from [drupal-digests](https://github.com/dbuytaert/drupal-digests), grouped by implementation phase. Use this to find what to work on next.

```
/rector-discover
/rector-discover --phase 2
/rector-discover --phase 1a --limit 5
```

Maintains `docs/rector-index.yml` as a derived index — regenerated automatically when absent or stale.

---

### `/rector-implement <digest-rule-file>`

Converts a single drupal-digests rule into a complete drupal-rector implementation with tests. Follows the 14-step canonical workflow in `.claude/skills/prompts/digest-to-rector-prompt.md` and enforces two additional quality gates:

- **QG-A — Type Guard Audit**: ensures every `MethodCall`/`PropertyFetch` node is guarded by `isObjectType()` to avoid false positives on untyped code.
- **QG-B — Version-Gating Tests**: for BC-wrapped rectors (`AbstractDrupalCoreRector`), adds a `testBelowVersion()` test and a `fixture-below-version/` fixture proving the transformation is suppressed on older Drupal versions.

```
/rector-implement repos/drupal-digests/rector/rules/replace-deprecated-sessionmanager-delete-with-3577376.php
```

---

### `/rector-qa <RectorClassName>`

Four-pass quality review of an existing rector. Use before merging or when auditing existing implementations.

| Pass | What it checks |
|------|---------------|
| 1 — Type Guard | `isObjectType()` guard present for all MethodCall/PropertyFetch nodes |
| 2 — Fixture Coverage | `basic.php.inc`, `no_change_unrelated.php.inc`, `fixture-below-version/` present as required |
| 3 — BC Decision | Base class (`AbstractRector` vs `AbstractDrupalCoreRector`) matches the deprecation's version and node type |
| 4 — @see URL | Docblock URL points to the correct Drupal.org issue or change record |

Produces a `PASS / WARN / FAIL` verdict per pass and an overall merge-readiness summary.

```
/rector-qa ReplaceSessionManagerDeleteRector
```

---

### `/rector-live-test <RectorClassName or issue-number>`

Finds real contrib modules that use the deprecated API a rector targets, then runs the rector against them to verify it transforms real-world code correctly. Uses [api.tresbien.tech](https://api.tresbien.tech) JSON API as primary search, falls back to the Drupal GitLab API.

```
/rector-live-test ReplaceSessionManagerDeleteRector
/rector-live-test 3577376
```

Results report files changed per module and flag zero-match cases with a diagnosis table (untyped code, wrong module version, cache, node type mismatch).

---

### `/rector-type-check-review [RectorClassName|all]`

Audits rector rules for type-specificity: ensures every `MethodCall`, `NullsafeMethodCall`, and `PropertyFetch` node is guarded by `isObjectType()` so unrelated classes with the same method/property name are not accidentally transformed.

Run on a single rector, or pass `all` (or no argument) to walk through every AT-RISK row in `.claude/skills/prompts/rector-type-specificity-checklist.md`. For each AT-RISK rector it finds the owning Drupal interface in `repos/drupal-core`, creates a stub if needed, adds the guard, and updates fixtures.

```
/rector-type-check-review ReplaceSessionManagerDeleteRector
/rector-type-check-review all
```

---

## Supporting scripts

Located in `.claude/scripts/` — shared utilities invoked by the skills above.

| Script | Purpose |
|--------|---------|
| `setup-repos.sh` | Clones `repos/drupal-digests` and `repos/drupal-core` (shallow). Pass `--update` to refresh. |
| `generate-rector-index.php` | Regenerates `docs/rector-index.yml` from the digests source. |

The live-test integration setup lives alongside its skill in `.claude/skills/rector-live-test/`:

| File | Purpose |
|------|---------|
| `setup-rector-test.sh` | Creates a DDEV Drupal 11 project with all contrib test modules wired to the local rector branch. |
| `drupal-rector-test/` | Companion directory for the generated test project. |

## Workflow

A typical cycle for adding a new rector:

```
/rector-discover --phase 2 --limit 1
  → picks the next pending rule

/rector-implement repos/drupal-digests/rector/rules/<digest-file>.php
  → writes rector class, fixture, test, config; runs phpstan + phpunit

/rector-live-test <ClassName>
  → validates against real contrib modules

/rector-qa <ClassName>
  → final four-pass quality check before opening a PR
```

## Requirements

- PHP 8.1+
- [DDEV](https://ddev.com) (for `setup-rector-test.sh` and running tests inside the container)
- Clone `repos/drupal-digests` and `repos/drupal-core` via `bash .claude/scripts/setup-repos.sh`
