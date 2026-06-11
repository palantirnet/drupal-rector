---
name: rector-discover
description: Lists unimplemented drupal-digests rules classified by implementation phase. Regenerates docs/rector-index.yml if absent or stale (>24h). Use to find what to work on next, filter by phase, or get a summary count.
argument-hint: "[--phase 1a|1b|1c|2|3|4] [--limit N] [--pending-only]"
allowed-tools: Bash, Read
---

# Rector Discover

Show which drupal-digests deprecation rules still need to be implemented in drupal-rector, grouped by phase.

## Steps

### 1. Ensure the digests repo is available

The canonical path is `repos/drupal-digests` (inside ddev: `/var/www/html/repos/drupal-digests`). Always run the setup script first to clone or update the repositories:

```bash
bash .claude/scripts/setup-repos.sh
```

### 2. Ensure the index is fresh

Update the `docs/rector-index.yml`:

```bash
INDEX="docs/rector-index.yml"
echo "Regenerating rector-index.yml…"
php .claude/scripts/generate-rector-index.php --digests-path=repos/drupal-digests
```

### 3. Read the index

Read `docs/rector-index.yml` completely.

### 3b. Apply the authoritative implemented-digests record

The index matcher is sometimes unstable and marks already-implemented digests as
`pending` (e.g. a rector whose `@see` cites a change-record number ≠ the digest
issue, or code that lives in an open PR not yet merged to main). The hand-maintained
`docs/implemented-digests.yml` is the source of truth for what is actually done, and
it always wins over the index.

Read that file (if it exists). For every issue under `digests:`, treat it as **not
pending** — its status there (`implemented` or `config-only`) overrides whatever the
index says.

```bash
[ -f docs/implemented-digests.yml ] && cat docs/implemented-digests.yml
```

### 4. Apply filters

If `$ARGUMENTS` contains `--phase X`, show only entries with `phase: 'X'`.
If `$ARGUMENTS` contains `--limit N`, show only the first N entries.
If `$ARGUMENTS` contains `--pending-only`, show only `status: pending` entries (default unless --all is passed).

Issues recorded in `implemented-digests.yml` (Step 3b) are never shown as pending.

### 5. Present results

Print a summary header (counts reflect Step 3b — subtract issues recorded in
`implemented-digests.yml` from `pending` and add them to `implemented`/`config-only`):
```
Rector Index — <timestamp>  (N entries from implemented-digests.yml applied)
  implemented: X   config-only: Y   pending: Z
```

Then list pending entries grouped by phase in order: 1a → 1b → 1c → 2 → 3 → 4 → unknown. Eg: `Phase 3 — Remove function call / node removal`

For each pending entry show:
```
[Phase 2] ReplaceSessionManagerDeleteRector — issue [#3577376](https://www.drupal.org/i/3577376)
  Digest: replace-deprecated-sessionmanager-delete-with-3577376.php
```

If all rules are implemented, print:
```
All rules are implemented or have config-only entries. Nothing pending.
```

### 6. Suggest next action

At the end, suggest the highest-priority pending rule to work on next (Phase 1a first, then 1b, 1c, 2, 3, 4):
```
Next suggested: /rector-implement repos/drupal-digests/rector/rules/<digest_file>
```

## Phase Reference

| Phase | Description                                                                     | Generic rector |
|-------|---------------------------------------------------------------------------------|----------------|
| 1a | FuncCall → service call (`fn(...)` ->  `\Drupal::service(...)`                   | `FunctionToServiceRector` |
| 1a | FuncCall → method on first arg (`fn($obj)` → `$obj->method()`)                  | `FunctionToFirstArgMethodRector` |
| 1a | Service ID rename (`\Drupal::service('old')` → `\Drupal::service('new')`)       | `DrupalServiceRenameRector` |
| 1b | FuncCall → static call on class (`fn(...)` -> `Class::method(...)`              | `FunctionToStaticRector` |
| 1c | Class constant → class constant (`OldClass::OLD` → `NewClass::NEW`)             | `ClassConstantToClassConstantRector` |
| 1c | Bare global constant → class constant (`DEPRECATED_CONST` → `\Ns\Class::CONST`) | `ConstantToClassConstantRector` |
| 2 | MethodCall rename with type check (`$obj->old()` → `$obj->new()`)               | `MethodToMethodWithCheckRector` |
| 2 | MethodCall custom transformation                                                | custom `AbstractRector` or `AbstractDrupalCoreRector` |
| 3 | Remove a function call statement with no replacement                            | `FunctionCallRemovalRector` |
| 3 | Node removal (other patterns)                                                   | custom class returning `REMOVE_NODE` |
| 4 | Complex / multi-node                                                            | custom class |
