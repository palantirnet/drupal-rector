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

The generator needs access to `drupal-digests`. In a ddev context, `~/` resolves to the container home directory, so the repo must be present at a path accessible inside the container. Check the most common locations:

```bash
# Check if the repo is accessible (inside ddev or on host)
DIGESTS_PATH=""
for candidate in \
    "/var/www/drupal-digests" \
    "../drupal-digests" \
    "$HOME/projects/drupal-digests"; do
  if [ -d "$candidate/rector/rules" ]; then
    DIGESTS_PATH="$candidate"
    break
  fi
done

if [ -z "$DIGESTS_PATH" ]; then
  echo "drupal-digests repo not found. Cloning to sibling directory…"
  cd ..
  git clone --depth=1 https://github.com/dbuytaert/drupal-digests.git
  cd drupal-rector
  DIGESTS_PATH="../drupal-digests"
fi
echo "Using digests repo: $DIGESTS_PATH"
```

**Tip:** To make `/var/www/drupal-digests` available permanently inside the ddev container, create `.ddev/docker-compose.digests.yaml` (gitignored — user-specific path):
```yaml
services:
  web:
    volumes:
      - "/absolute/path/to/drupal-digests:/var/www/drupal-digests:ro"
```
Then run `ddev restart`. On subsequent runs the first candidate path will match and no clone is needed.

```bash
```

### 2. Ensure the index is fresh

Check whether `docs/rector-index.yml` exists and is less than 24 hours old:

```bash
INDEX="docs/rector-index.yml"
if [ ! -f "$INDEX" ] || [ "$(find "$INDEX" -mmin +1440 2>/dev/null)" ]; then
  echo "Regenerating rector-index.yml…"
  php scripts/generate-rector-index.php --digests-path="$DIGESTS_PATH"
else
  echo "Using existing index ($(date -r "$INDEX" '+%Y-%m-%d %H:%M'))"
fi
```

### 3. Read the index

Read `docs/rector-index.yml` completely.

### 4. Apply filters

If `$ARGUMENTS` contains `--phase X`, show only entries with `phase: 'X'`.
If `$ARGUMENTS` contains `--limit N`, show only the first N entries.
If `$ARGUMENTS` contains `--pending-only`, show only `status: pending` entries (default unless --all is passed).

### 5. Present results

Print a summary header:
```
Rector Index — <timestamp>
  implemented: X   config-only: Y   pending: Z
```

Then list pending entries grouped by phase in order: 1a → 1b → 1c → 2 → 3 → 4 → unknown.

For each pending entry show:
```
[Phase 2] ReplaceSessionManagerDeleteRector — issue #3577376
  Digest: replace-deprecated-sessionmanager-delete-with-3577376.php
```

If all rules are implemented, print:
```
All rules are implemented or have config-only entries. Nothing pending.
```

### 6. Suggest next action

At the end, suggest the highest-priority pending rule to work on next (Phase 1a first, then 1b, 1c, 2, 3, 4):
```
Next suggested: /rector-implement ~/projects/drupal-digests/rector/rules/<digest_file>
```

## Phase Reference

| Phase | Description | Generic rector |
|-------|-------------|----------------|
| 1a | FuncCall → service call | `FunctionToServiceRector` |
| 1b | FuncCall → static call | `FunctionToStaticRector` |
| 1c | Class constant replacement | `ClassConstantToClassConstantRector` |
| 2 | MethodCall custom class | custom `AbstractRector` or `AbstractDrupalCoreRector` |
| 3 | Node removal | custom class returning `REMOVE_NODE` |
| 4 | Complex / multi-node | custom class |

## Refreshing manually

To force a full regeneration:
```bash
php scripts/generate-rector-index.php
```

The generated file is gitignored — it's always derived from source.
